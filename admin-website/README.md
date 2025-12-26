# GroceryPlus Web Admin Panel

## Overview

A modern, responsive web-based admin panel for GroceryPlus that connects to the REST API. This provides a complete administrative interface without direct database access.

## 🚀 Quick Start

1. **Open in Browser**: Navigate to `admin-website/index.html`
2. **Login**: Use admin credentials (email: admin@groceryplus.com, password: admin123)
3. **Manage**: Access all admin functions through the web interface

## 📋 Features

### ✅ **Fully Implemented**
- **Authentication**: Secure admin login with API tokens
- **Dashboard**: Real-time metrics and statistics
- **Product Management**: Add, edit, delete products
- **Category Management**: Manage product categories
- **Order Management**: View and update order status
- **User Management**: Admin user controls
- **Analytics**: Comprehensive business insights

### 🎨 **User Experience**
- **Responsive Design**: Works on desktop and mobile
- **Modern UI**: Bootstrap 5 with custom styling
- **Real-time Updates**: Live data from API
- **Error Handling**: User-friendly error messages
- **Loading States**: Professional loading indicators

## 🏗️ Architecture

```
Web Admin Panel (HTML/JS)
        ↓
    REST API (PHP)
        ↓
   SQLite Database
```

**Benefits over PHP Admin:**
- ✅ **API Consistency**: Same endpoints as mobile app
- ✅ **Scalability**: Can be hosted separately
- ✅ **Security**: No direct database exposure
- ✅ **Maintainability**: Single API layer
- ✅ **Modern UX**: Rich JavaScript interface

## 📁 File Structure

```
admin-website/
├── index.html              # Main admin interface
├── test.html               # API testing page
├── css/
│   └── style.css          # Custom styles
├── js/
│   ├── config.js          # API configuration
│   ├── auth.js            # Authentication
│   ├── app.js             # Core functionality
│   ├── dashboard.js       # Dashboard logic
│   ├── products.js        # Product management
│   ├── categories.js      # Category management
│   ├── orders.js          # Order management
│   ├── users.js           # User management
│   └── analytics.js       # Analytics dashboard
└── README.md              # This documentation
```

## 🔧 API Integration

All operations use your existing API endpoints:

```javascript
// Example: Get products
const response = await api.getProducts();
const products = response.data.products;

// Example: Create product
await api.createProduct({
    product_name: "New Product",
    price: 9.99,
    category_id: 1
});
```

## 🧪 Testing

### Quick API Test
Open `admin-website/test.html` to verify API connectivity:
- ✅ Products API
- ✅ Categories API
- ✅ Analytics API (admin)

### Full Admin Test
1. Open `admin-website/index.html`
2. Login with admin credentials
3. Navigate through all sections
4. Test CRUD operations

## 🔒 Security Features

- **Token-based Authentication**: Secure API access
- **Session Management**: Automatic logout on token expiry
- **Input Validation**: Client and server-side validation
- **Error Sanitization**: No sensitive data exposure

## 🚀 Deployment

### Local Development
```bash
# Ensure PHP server is running
# Open admin-website/index.html in browser
```

### Production Deployment
1. **Host static files** on any web server
2. **Update API URL** in `js/config.js`
3. **Configure CORS** on API server
4. **Use HTTPS** for security

## 🐛 Troubleshooting

### Common Issues

**"API Connection Failed"**
- Check if PHP backend is running
- Verify API_BASE_URL in config.js
- Check browser console for CORS errors

**"Login Failed"**
- Verify admin credentials in database
- Check API auth endpoint response
- Clear browser localStorage

**"Data Not Loading"**
- Check browser developer tools network tab
- Verify API endpoints are accessible
- Check for JavaScript errors

### Debug Mode
Enable verbose logging in browser console:
```javascript
localStorage.setItem('debug', 'true');
```

## 🔄 Migration from PHP Admin

If migrating from the PHP admin panel:

1. **Data Export**: Export any custom data
2. **URL Updates**: Update any hardcoded links
3. **User Training**: Train admins on new interface
4. **Testing**: Thoroughly test all workflows

## 🎯 Future Enhancements

- **Real-time Notifications**: WebSocket integration
- **Advanced Charts**: Interactive data visualizations
- **Bulk Operations**: Mass update/delete
- **Export Features**: CSV/PDF reports
- **Audit Logs**: Admin action tracking

## 📞 Support

- **API Documentation**: See main project README
- **Browser Compatibility**: Chrome, Firefox, Safari, Edge
- **Mobile Support**: Responsive design for tablets

---

**Ready to manage your GroceryPlus store through a modern web interface! 🎉**