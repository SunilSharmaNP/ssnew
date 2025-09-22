<div class="contact-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-envelope"></i> Contact Us</h1>
            <p>Get in touch with us for any questions or concerns</p>
        </div>
        
        <div class="contact-content">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>We're here to help! Reach out to us through any of the following methods:</p>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <i class="fas fa-envelope"></i>
                        <h3>Email</h3>
                        <p>support@flipmart.com</p>
                    </div>
                    
                    <div class="contact-method">
                        <i class="fas fa-phone"></i>
                        <h3>Phone</h3>
                        <p>+91 12345 67890</p>
                    </div>
                    
                    <div class="contact-method">
                        <i class="fas fa-clock"></i>
                        <h3>Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM<br>
                        Saturday: 10:00 AM - 4:00 PM</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-section">
                <h2>Send us a Message</h2>
                <form class="contact-form" method="POST" action="api/contact.php">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.contact-page {
    padding: 2rem 0;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.page-header h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #667eea;
}

.page-header i {
    margin-right: 0.5rem;
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1000px;
    margin: 0 auto;
}

.contact-info,
.contact-form-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.theme-dark .contact-info,
.theme-dark .contact-form-section {
    background: #21262d;
    border: 1px solid #30363d;
}

.contact-info h2,
.contact-form-section h2 {
    color: #667eea;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.contact-methods {
    margin-top: 2rem;
}

.contact-method {
    margin-bottom: 2rem;
}

.contact-method i {
    font-size: 1.5rem;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.contact-method h3 {
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.theme-dark .contact-method h3 {
    color: #c9d1d9;
}

.contact-method p {
    color: #6c757d;
    line-height: 1.6;
}

.theme-dark .contact-method p {
    color: #8b949e;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
}

.theme-dark .form-group label {
    color: #c9d1d9;
}

.form-group input,
.form-group textarea {
    padding: 12px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.theme-dark .form-group input,
.theme-dark .form-group textarea {
    background: #0d1117;
    border-color: #30363d;
    color: #c9d1d9;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

@media (max-width: 768px) {
    .contact-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}
</style>