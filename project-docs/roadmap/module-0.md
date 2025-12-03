# Module 0: Initial Setup & Configuration

**Duration:** 3-4 days  
**Dependencies:** None  
**Objective:** Set up development environment, Laravel project structure, and CI/CD pipeline

---

## Development Principles

1. **Complete Each Module Before Moving to Next**
2. **Test Thoroughly After Each Module**
3. **Document As You Build**
4. **Commit Frequently with Descriptive Messages**
5. **Review and Refactor Before Next Module**

---

## Tasks Checklist

### GitHub Setup

- [ ] Create organization: `GFZA-IOP`
- [ ] Create main repository: `gfza-staff-portal`
- [ ] Create branch protection rules on `main`
  - Require pull request reviews (minimum 2)
  - Require status checks before merge
  - Dismiss stale PR approvals
- [ ] Create branches:
  - `main` - production ready code
  - `staging` - pre-production testing
  - `develop` - integration branch
  - `feature/*` - feature branches
- [ ] Set up GitHub Actions for CI/CD
  - Auto-run tests on PR
  - Lint checking
  - Code coverage reports
- [ ] Create `.gitignore` for Laravel
- [ ] Add README with setup instructions
- [ ] Create CONTRIBUTING.md guidelines
- [ ] Set up issue templates (Bug, Feature Request, Documentation)
- [ ] Configure branch naming conventions

---

## Module 0 Testing Checklist

```
✓ Laravel app runs on local environment
✓ Database migrations successful
✓ Seeding works (roles, permissions, initial admin)
✓ Artisan commands functional
✓ Mail configuration tested (log driver for dev)
✓ Cache drivers configured
✓ Environment setup documented
✓ GitHub repo initialized with all branches
✓ CI/CD pipeline passes
✓ README and CONTRIBUTING docs complete
✓ Documentation organized
```

---

## Estimated Timeline

**Duration:** 3-4 days

---

## Next Module

→ [Module 1: User Management & Employee Directory](./module-1.md)
