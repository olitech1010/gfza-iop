<style>
    /* Import Poppins font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    /* Apply Poppins font globally */
    body, .fi-body {
        font-family: 'Poppins', sans-serif !important;
    }
    
    /* Login page background styling */
    .fi-simple-layout {
        background: linear-gradient(135deg, rgba(0, 199, 63, 0.03) 0%, rgba(255, 255, 255, 0.98) 40%), 
                    url('/images/login-background.png');
        background-size: cover;
        background-position: center right;
        background-attachment: fixed;
    }
    
    /* Login form card styling */
    .fi-simple-main {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }
    
    /* Sidebar navigation styling */
    .fi-sidebar-item-label {
        font-weight: 500;
    }
    
    /* Primary button styling */
    .fi-btn-primary {
        background-color: #00c73f !important;
    }
    
    .fi-btn-primary:hover {
        background-color: #00b035 !important;
    }
    
    /* Accent color for links and focus states */
    .fi-link {
        color: #00c73f !important;
    }
    
    /* Form input focus states */
    .fi-input:focus {
        border-color: #00c73f !important;
        --tw-ring-color: rgba(0, 199, 63, 0.2) !important;
    }
</style>
