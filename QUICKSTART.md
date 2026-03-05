# 🚀 Quick Start Guide - DreamEvents

## Get Started in 3 Easy Steps!

### ⚡ Step 1: Install Database (1 minute)
1. Make sure XAMPP is running (Apache + MySQL)
2. Open your browser
3. Go to: **http://localhost/events-management/config/install.php**
4. Wait for "Installation completed successfully!" message

### 🌐 Step 2: Visit Your Website
**Main Website**: http://localhost/events-management/index.php

Navigate through:
- ✅ Home - View services and features
- ✅ Packages - Browse event packages
- ✅ Gallery - View event photos
- ✅ About - Learn about the company
- ✅ Contact - Get in touch
- ✅ Book Now - Make a booking

### 🔐 Step 3: Access Admin Panel
**Admin URL**: http://localhost/events-management/admin/login.php

**Login Credentials:**
```
Username: admin
Password: admin123
```

## 📊 Admin Panel Features

Once logged in, you can:

1. **Dashboard** - View statistics and recent bookings
2. **Bookings** - Manage all customer bookings
   - View booking details
   - Update booking status
   - Track payments
3. **Packages** - Manage event packages
   - Add new packages
   - Edit existing packages
   - Set prices and features
4. **Gallery** - Upload and manage photos
5. **Messages** - Read contact form submissions
6. **Settings** - Update system settings

## 💡 Tips for Testing

### Test the Booking System:
1. Go to Packages page
2. Click "Book Now" on any package
3. Fill in the booking form:
   - Select package
   - Enter your details
   - Choose event date/time
   - Select payment method
4. Submit and check admin panel for the booking

### Test Contact Form:
1. Go to Contact page
2. Fill in the contact form
3. Submit message
4. Check admin panel Messages section

## 🎨 Customization Quick Tips

### Change Website Colors:
Edit any CSS file and modify these variables:
```css
:root {
    --primary-color: #ff6b9d;      /* Main pink color */
    --secondary-color: #c44569;    /* Darker pink */
    --dark-color: #2c3e50;         /* Dark blue-gray */
}
```

### Change Company Name:
1. Open `index.php`
2. Find "DreamEvents"
3. Replace with your company name
4. Repeat for other pages

### Update Contact Information:
Edit `contact.php` and update:
- Address
- Phone numbers
- Email addresses
- Business hours

## 🔧 Troubleshooting

### Database Connection Error?
1. Check if MySQL is running in XAMPP
2. Verify database credentials in `config/database.php`
3. Re-run installation: http://localhost/events-management/config/install.php

### Page Not Found?
1. Check XAMPP Apache is running
2. Verify you're using correct URL: http://localhost/events-management/
3. Check file exists in htdocs folder

### Can't Login to Admin?
1. Use exact credentials: admin / admin123
2. Clear browser cache
3. Re-run installation to reset admin password

## 📱 Mobile Testing

Test on mobile:
1. Find your computer's local IP (e.g., 192.168.1.100)
2. On mobile browser, go to: http://YOUR_IP/events-management/
3. All pages are mobile-responsive!

## 🎯 Next Steps

1. ✅ Add your own event packages
2. ✅ Upload real photos to gallery
3. ✅ Customize colors and branding
4. ✅ Update company information
5. ✅ Test booking process thoroughly
6. ✅ Change admin password for security

## 📞 Need Help?

- Check README.md for detailed documentation
- Review database schema in config/install.php
- Inspect browser console for JavaScript errors
- Check PHP error logs in XAMPP

---

**Happy Event Planning! 🎉**

Your professional website is ready to accept bookings!
