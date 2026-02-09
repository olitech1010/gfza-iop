<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppraisalResource\Pages;
use App\Models\Appraisal;
use App\Models\AppraisalPeriod;
use App\Models\User;
use App\Services\AppraisalScoringService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class AppraisalResource extends Resource
{
    protected static ?string $model = Appraisal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'HR Operations';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '!=', 'completed')
            ->where(function ($query) {
                $user = auth()->user();
                if ($user->hasRole('hr_manager')) return $query;
                if ($user->hasRole('dept_head')) return $query->where('hod_id', $user->id);
                return $query->where('user_id', $user->id);
            })->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: Personal Info
                Forms\Components\Section::make('Section 1: Personal Details')
                    ->schema([
                        Forms\Components\Select::make('appraisal_period_id')
                            ->label('Appraisal Period')
                            ->options(AppraisalPeriod::where('is_active', true)->pluck('title', 'id'))
                            ->default(AppraisalPeriod::where('is_active', true)->value('id'))
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                            
                        Forms\Components\Select::make('user_id')
                            ->label('Employee')
                            ->options(User::all()->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('job_title')
                            ->label('Job Title')
                            ->default(auth()->user()->job_title)
                            ->required(),

                        Forms\Components\TextInput::make('current_grade')
                            ->label('Current Grade')
                            ->required(),

                        Forms\Components\DatePicker::make('date_appointed_present_grade')
                            ->label('Date Appointed to Present Grade')
                            ->required(),
                            
                        Forms\Components\Select::make('hod_id')
                            ->label('Head of Department (Reviewer)')
                            ->options(User::role('dept_head')->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->disabled(fn ($record) => $record && $record->status !== 'goal_setting'),
                    ])->columns(2),

                // SECTION 1-A: Training
                Forms\Components\Section::make('Section 1-A: Training History')
                    ->schema([
                        Forms\Components\Repeater::make('trainings')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('institution')->required(),
                                Forms\Components\TextInput::make('program_name')->required(),
                                Forms\Components\DatePicker::make('date')->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->disabled(fn ($record) => $record && $record->status !== 'goal_setting'),
                    ]),

                // SECTION 4: Targets
                Forms\Components\Section::make('Section 4: Work Targets')
                    ->description('Set your objectives for the period.')
                    ->schema([
                        Forms\Components\Repeater::make('targets')
                            ->relationship()
                            ->schema([
                                Forms\Components\Textarea::make('objective')
                                    ->label('Objective')
                                    ->required()
                                    ->columnSpan(2)
                                    ->disabled(fn ($get) => $get('../../status') !== 'goal_setting' && $get('../../status') !== null),
                                    
                                Forms\Components\Textarea::make('target_criteria')
                                    ->label('Target / Criteria')
                                    ->required()
                                    ->columnSpan(2)
                                    ->disabled(fn ($get) => $get('../../status') !== 'goal_setting' && $get('../../status') !== null),
                                
                                // HOD Scoring Fields
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('manager_score')
                                            ->label('HOD Score (1-5)')
                                            ->options([
                                                1 => '1 - Unacceptable',
                                                2 => '2 - Below Expectation',
                                                3 => '3 - Meets Expectations',
                                                4 => '4 - Exceeds Expectations',
                                                5 => '5 - Exceptional',
                                            ])
                                            ->default(3)
                                            ->validationMessages([
                                                'required_if' => 'Evidence/Remarks are required for scores of 1 or 5.',
                                            ])
                                            ->live()
                                            ->helperText(fn ($state) => match ($state) {
                                                1 => 'Has not at all demonstrated this behavior/competency. Evidence: 3 or more examples (of failure).',
                                                2 => 'Has rarely demonstrated this behavior/competency. Evidence: 2 or more examples (of underperformance).',
                                                3 => 'Has demonstrated this behavior/competency. Evidence: At least 2 examples.',
                                                4 => 'Has frequently demonstrated this behavior/competency. Evidence: 3 or more examples.',
                                                5 => 'Has consistently demonstrated this behavior/competency. Evidence: 4 or more examples.',
                                                default => 'Select a score to see definition.'
                                            })
                                            ->visible(fn ($get) => $get('../../status') === 'hod_review' || $get('../../status') === 'hr_review' || $get('../../status') === 'completed')
                                            ->disabled(fn ($get) => $get('../../status') !== 'hod_review'),

                                        Forms\Components\Textarea::make('remarks')
                                            ->label('Remarks / Evidence')
                                            ->required(fn ($get) => in_array($get('manager_score'), [1, 5]))
                                            ->visible(fn ($get) => $get('../../status') === 'hod_review' || $get('../../status') === 'hr_review' || $get('../../status') === 'completed')
                                            ->disabled(fn ($get) => $get('../../status') !== 'hod_review'),
                                    ]),
                            ])
                            ->columns(4)
                            ->addable(fn ($get) => $get('status') === 'goal_setting' || $get('status') === null)
                            ->deletable(fn ($get) => $get('status') === 'goal_setting' || $get('status') === null),
                    ]),

                // SECTION 5 & D: Competencies (Only Visible to HOD/HR)
                Forms\Components\Section::make('Competency Assessment')
                    ->visible(fn ($record) => $record && in_array($record->status, ['hod_review', 'hr_review', 'completed']))
                    ->schema([
                        Forms\Components\Repeater::make('competencyScores')
                            ->relationship()
                            ->label('Competencies')
                            ->schema([
                                Forms\Components\TextInput::make('competency_name')
                                    ->disabled()
                                    ->columnSpan(2),
                                Forms\Components\Hidden::make('competency_type'),
                                
                                Forms\Components\Select::make('manager_score')
                                    ->label('Score')
                                    ->options([
                                        1 => '1 - Unacceptable',
                                        2 => '2 - Below Expectation',
                                        3 => '3 - Meets Expectations',
                                        4 => '4 - Exceeds Expectations',
                                        5 => '5 - Exceptional',
                                    ])
                                    ->default(3)
                                    ->live()
                                    ->helperText(fn ($state) => match ($state) {
                                        1 => 'Has not at all demonstrated this behavior/competency. Evidence: 3 or more examples (of failure).',
                                        2 => 'Has rarely demonstrated this behavior/competency. Evidence: 2 or more examples (of underperformance).',
                                        3 => 'Has demonstrated this behavior/competency. Evidence: At least 2 examples.',
                                        4 => 'Has frequently demonstrated this behavior/competency. Evidence: 3 or more examples.',
                                        5 => 'Has consistently demonstrated this behavior/competency. Evidence: 4 or more examples.',
                                        default => 'Select a score to see definition.'
                                    })
                                    ->disabled(fn ($get) => $get('../../status') !== 'hod_review')
                                    ->required(),

                                Forms\Components\Textarea::make('remarks')
                                    ->label('Evidence')
                                    ->required(fn ($get) => in_array($get('manager_score'), [1, 5]))
                                    ->disabled(fn ($get) => $get('../../status') !== 'hod_review'),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->columns(2),
                            
                        Forms\Components\Section::make('Final HOD Verdict')
                            ->schema([
                                Forms\Components\Select::make('promotion_verdict')
                                    ->options([
                                        'outstanding' => 'Outstanding',
                                        'suitable' => 'Suitable for Promotion',
                                        'ready_2_3_years' => 'Ready in 2-3 Years',
                                        'not_ready' => 'Not Ready',
                                        'unlikely' => 'Unlikely to be Ready',
                                    ])
                                    ->required()
                                    ->disabled(fn ($record) => $record->status !== 'hod_review'),
                                    
                                Forms\Components\Textarea::make('appraiser_comment')
                                    ->label('HOD Final Comments')
                                    ->disabled(fn ($record) => $record->status !== 'hod_review'),
                            ]),
                            
                        Forms\Components\Placeholder::make('score_display')
                            ->label('Projected Final Score')
                            ->content(fn ($record) => $record ? $record->final_score : '0.00'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->searchable(),
                Tables\Columns\TextColumn::make('period.title')
                    ->label('Period'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'goal_setting',
                        'warning' => 'hod_review',
                        'info' => 'hr_review',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('final_score')
                    ->label('Score')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period')
                    ->relationship('period', 'title'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'goal_setting' => 'Goal Setting',
                        'hod_review' => 'HOD Review',
                        'hr_review' => 'HR Review',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                // Submit to HOD Action (Staff)
                Tables\Actions\Action::make('submit_to_hod')
                    ->label('Submit to HOD')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn (Appraisal $record) => $record->status === 'goal_setting' && $record->user_id === auth()->id())
                    ->requiresConfirmation()
                    ->action(function (Appraisal $record) {
                        if ($record->targets()->count() === 0) {
                            Notification::make()
                                ->title('Cannot Submit')
                                ->body('Please add at least one work target before submitting.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $record->update(['status' => 'hod_review']);
                        Notification::make()->title('Appraisal submitted to HOD')->success()->send();
                    }),

                // Submit to HR Action (HOD)
                Tables\Actions\Action::make('submit_to_hr')
                    ->label('Submit to HR')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Appraisal $record) => $record->status === 'hod_review' && $record->hod_id === auth()->id())
                    ->requiresConfirmation()
                    ->action(function (Appraisal $record) {
                        // Calculate final score before submitting
                        $service = new AppraisalScoringService();
                        $score = $service->calculateScore($record);
                        $record->update([
                            'status' => 'hr_review',
                            'final_score' => $score
                        ]);
                        Notification::make()->title('Submitted to HR. Final Score: ' . $score)->success()->send();
                    }),

                // Complete Action (HR)
                Tables\Actions\Action::make('complete')
                    ->label('Finalize')
                    ->icon('heroicon-o-lock-closed')
                    ->visible(fn (Appraisal $record) => $record->status === 'hr_review' && auth()->user()->hasRole(['hr_manager', 'super_admin']))
                    ->requiresConfirmation()
                    ->action(function (Appraisal $record) {
                        $record->update(['status' => 'completed']);
                        Notification::make()->title('Appraisal Completed')->success()->send();
                    }),

                // Download PDF Action
                Tables\Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Appraisal $record) => route('appraisal.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole(['super_admin', 'hr_manager'])) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('hod_id', $user->id);
        });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppraisals::route('/'),
            'create' => Pages\CreateAppraisal::route('/create'),
            'view' => Pages\ViewAppraisal::route('/{record}'),
            'edit' => Pages\EditAppraisal::route('/{record}/edit'),
        ];
    }
}
