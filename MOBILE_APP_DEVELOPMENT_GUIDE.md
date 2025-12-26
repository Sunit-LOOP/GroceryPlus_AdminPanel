# GroceryPlus Mobile App Development Guide

## Overview

GroceryPlus is a complete e-commerce solution with:
- **REST API Backend** (PHP + SQLite)
- **Admin Panel** (Web-based management interface)
- **Mobile App** (Client-side application)

This guide will help you build the mobile app that connects to the existing API.

## System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Mobile App    │────│   REST API      │────│  Admin Panel    │
│   (React Native/│    │   (PHP)         │    │  (Web)          │
│    Flutter)     │    │                 │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐
                       │   SQLite DB     │
                       │                 │
                       └─────────────────┘
```

## API Base URL
```
http://YOUR_SERVER_IP/groceryplus/api
```

## Authentication

### User Registration
```http
POST /register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "+977-9841234567"
}
```

**Response:**
```json
{
  "success": true,
  "status": 201,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+977-9841234567",
      "type": "customer"
    },
    "token": "user_1_1234567890_abcdef123456",
    "message": "Registration successful"
  }
}
```

### User Login
```http
POST /auth
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "type": "customer"
    },
    "token": "user_1_1234567890_abcdef123456"
  }
}
```

## Products API

### Get All Products
```http
GET /products
Authorization: Bearer YOUR_TOKEN
```

**Query Parameters:**
- `category_id` - Filter by category
- `search` - Search in product names
- `limit` - Number of products (default: 50)
- `offset` - Pagination offset (default: 0)

**Response:**
```json
{
  "success": true,
  "status": 200,
  "data": {
    "products": [
      {
        "product_id": "1",
        "product_name": "Apple",
        "category_id": "1",
        "price": "1.5",
        "description": "Fresh red apple",
        "image": null,
        "stock_quantity": "100",
        "vendor_id": "1",
        "category_name": "Fruits",
        "vendor_name": "Local Farm",
        "review_count": 0,
        "average_rating": 0,
        "image_url": null
      }
    ],
    "count": 3,
    "limit": 50,
    "offset": 0
  }
}
```

### Get Single Product
```http
GET /products/{product_id}
Authorization: Bearer YOUR_TOKEN
```

## Categories API

### Get All Categories
```http
GET /categories
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "data": {
    "categories": [
      {
        "category_id": "1",
        "category_name": "Fruits",
        "category_description": "Fresh fruits"
      }
    ]
  }
}
```

## Shopping Cart API

### Get User Cart
```http
GET /cart
Authorization: Bearer YOUR_TOKEN
```

### Add to Cart
```http
POST /cart
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

### Update Cart Item
```http
PUT /cart/{cart_id}
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "quantity": 3
}
```

### Remove from Cart
```http
DELETE /cart/{cart_id}
Authorization: Bearer YOUR_TOKEN
```

## Orders API

### Get User Orders
```http
GET /orders
Authorization: Bearer YOUR_TOKEN
```

### Create Order
```http
POST /orders
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "cart_items": [
    {"product_id": 1, "quantity": 2},
    {"product_id": 2, "quantity": 1}
  ],
  "delivery_address": "Kathmandu, Nepal"
}
```

### Update Order Status
```http
PUT /orders/{order_id}
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "status": "delivered"
}
```

## Favorites API

### Get User Favorites
```http
GET /favorites
Authorization: Bearer YOUR_TOKEN
```

### Add to Favorites
```http
POST /favorites
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "product_id": 1
}
```

### Remove from Favorites
```http
DELETE /favorites/{favorite_id}
Authorization: Bearer YOUR_TOKEN
```

## Reviews API

### Get Product Reviews
```http
GET /reviews?product_id=1
```

### Submit Review
```http
POST /reviews
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "product_id": 1,
  "rating": 5,
  "comment": "Great product!"
}
```

## Messages API

### Get User Messages
```http
GET /messages
Authorization: Bearer YOUR_TOKEN
```

### Send Message
```http
POST /messages
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "receiver_id": 2,
  "message": "Hello!"
}
```

## Notifications API

### Get User Notifications
```http
GET /notifications
Authorization: Bearer YOUR_TOKEN
```

## Mobile App Development

### Technology Stack Recommendations

#### React Native (Recommended)
```bash
npx react-native init GroceryPlusApp
cd GroceryPlusApp
npm install @react-navigation/native axios async-storage
```

#### Flutter
```bash
flutter create grocery_plus_app
cd grocery_plus_app
flutter pub add http shared_preferences
```

### Project Structure
```
mobile-app/
├── src/
│   ├── api/
│   │   ├── client.js
│   │   └── endpoints.js
│   ├── components/
│   │   ├── ProductCard.js
│   │   ├── CartItem.js
│   │   └── ...
│   ├── screens/
│   │   ├── HomeScreen.js
│   │   ├── ProductScreen.js
│   │   ├── CartScreen.js
│   │   ├── ProfileScreen.js
│   │   └── ...
│   ├── navigation/
│   │   └── AppNavigator.js
│   ├── utils/
│   │   ├── storage.js
│   │   └── constants.js
│   └── context/
│       └── AuthContext.js
├── assets/
│   └── images/
└── App.js
```

### API Client Setup

#### React Native API Client
```javascript
// src/api/client.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'http://YOUR_SERVER_IP/groceryplus/api';

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
});

// Request interceptor for auth token
apiClient.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('userToken');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired, logout user
      AsyncStorage.removeItem('userToken');
      // Navigate to login
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

#### API Endpoints
```javascript
// src/api/endpoints.js
import apiClient from './client';

export const authAPI = {
  register: (userData) => apiClient.post('/register', userData),
  login: (credentials) => apiClient.post('/auth', credentials),
};

export const productsAPI = {
  getAll: (params) => apiClient.get('/products', { params }),
  getById: (id) => apiClient.get(`/products/${id}`),
};

export const cartAPI = {
  getCart: () => apiClient.get('/cart'),
  addToCart: (item) => apiClient.post('/cart', item),
  updateCart: (id, data) => apiClient.put(`/cart/${id}`, data),
  removeFromCart: (id) => apiClient.delete(`/cart/${id}`),
};

export const ordersAPI = {
  getOrders: () => apiClient.get('/orders'),
  createOrder: (orderData) => apiClient.post('/orders', orderData),
  updateOrder: (id, data) => apiClient.put(`/orders/${id}`, data),
};
```

### Authentication Context
```javascript
// src/context/AuthContext.js
import React, { createContext, useState, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { authAPI } from '../api/endpoints';

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkAuthState();
  }, []);

  const checkAuthState = async () => {
    const token = await AsyncStorage.getItem('userToken');
    const userData = await AsyncStorage.getItem('userData');

    if (token && userData) {
      setUser(JSON.parse(userData));
    }
    setLoading(false);
  };

  const login = async (email, password) => {
    try {
      const response = await authAPI.login({ email, password });
      const { token, user: userData } = response.data.data;

      await AsyncStorage.setItem('userToken', token);
      await AsyncStorage.setItem('userData', JSON.stringify(userData));

      setUser(userData);
      return { success: true };
    } catch (error) {
      return { success: false, error: error.response?.data?.message };
    }
  };

  const register = async (userData) => {
    try {
      const response = await authAPI.register(userData);
      const { token, user: newUser } = response.data.data;

      await AsyncStorage.setItem('userToken', token);
      await AsyncStorage.setItem('userData', JSON.stringify(newUser));

      setUser(newUser);
      return { success: true };
    } catch (error) {
      return { success: false, error: error.response?.data?.message };
    }
  };

  const logout = async () => {
    await AsyncStorage.removeItem('userToken');
    await AsyncStorage.removeItem('userData');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{
      user,
      loading,
      login,
      register,
      logout,
      isAuthenticated: !!user,
    }}>
      {children}
    </AuthContext.Provider>
  );
};
```

### Key Screens to Implement

#### 1. Authentication Screens
- LoginScreen
- RegisterScreen
- ForgotPasswordScreen (if implemented)

#### 2. Main App Screens
- HomeScreen (Product listing)
- ProductDetailsScreen
- CategoryScreen
- SearchScreen

#### 3. Shopping Screens
- CartScreen
- CheckoutScreen
- OrderHistoryScreen
- OrderDetailsScreen

#### 4. User Screens
- ProfileScreen
- FavoritesScreen
- MessagesScreen
- NotificationsScreen

### Navigation Setup
```javascript
// src/navigation/AppNavigator.js
import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { AuthContext } from '../context/AuthContext';

// Import screens
import LoginScreen from '../screens/LoginScreen';
import RegisterScreen from '../screens/RegisterScreen';
import HomeScreen from '../screens/HomeScreen';
// ... other imports

const Stack = createStackNavigator();
const Tab = createBottomTabNavigator();

const TabNavigator = () => (
  <Tab.Navigator>
    <Tab.Screen name="Home" component={HomeScreen} />
    <Tab.Screen name="Categories" component={CategoryScreen} />
    <Tab.Screen name="Cart" component={CartScreen} />
    <Tab.Screen name="Profile" component={ProfileScreen} />
  </Tab.Navigator>
);

const AppNavigator = () => {
  const { isAuthenticated, loading } = React.useContext(AuthContext);

  if (loading) {
    return <LoadingScreen />;
  }

  return (
    <NavigationContainer>
      <Stack.Navigator>
        {isAuthenticated ? (
          <Stack.Screen
            name="MainTabs"
            component={TabNavigator}
            options={{ headerShown: false }}
          />
        ) : (
          <>
            <Stack.Screen name="Login" component={LoginScreen} />
            <Stack.Screen name="Register" component={RegisterScreen} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
};

export default AppNavigator;
```

## Admin Panel Integration

### Admin API Access
The admin panel provides a web interface for:
- Product management
- Category management
- User management
- Order management
- Analytics and reports

### Connecting Mobile App to Admin Panel
1. **Real-time Updates**: Use polling or websockets for live data
2. **Push Notifications**: Integrate FCM/APNs for order updates
3. **Admin Notifications**: Alerts for new orders, low stock, etc.

## Development Best Practices

### Error Handling
```javascript
const handleApiCall = async (apiFunction) => {
  try {
    const response = await apiFunction();
    return { success: true, data: response.data };
  } catch (error) {
    const message = error.response?.data?.message || 'An error occurred';
    return { success: false, error: message };
  }
};
```

### Loading States
```javascript
const [loading, setLoading] = useState(false);

const fetchProducts = async () => {
  setLoading(true);
  const result = await handleApiCall(() => productsAPI.getAll());
  setLoading(false);

  if (result.success) {
    setProducts(result.data.products);
  } else {
    Alert.alert('Error', result.error);
  }
};
```

### Data Caching
```javascript
const [products, setProducts] = useState([]);
const [lastFetch, setLastFetch] = useState(null);

const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

const getProducts = async (force = false) => {
  const now = Date.now();

  if (!force && lastFetch && (now - lastFetch) < CACHE_DURATION) {
    return products; // Return cached data
  }

  const result = await handleApiCall(() => productsAPI.getAll());
  if (result.success) {
    setProducts(result.data.products);
    setLastFetch(now);
    return result.data.products;
  }

  return [];
};
```

## Testing

### API Testing
```bash
# Run API tests
cd api
php api_test.php
```

### Mobile App Testing
```bash
# React Native
npm test
npx react-native run-android
npx react-native run-ios

# Flutter
flutter test
flutter run
```

## Deployment

### Server Setup
1. Deploy PHP API to web server
2. Configure database path in `api/src/includes/db.php`
3. Update CORS settings for mobile app domains
4. Set up SSL certificate

### Mobile App Deployment
1. Update API_BASE_URL to production server
2. Configure push notifications
3. Build and submit to app stores

## Troubleshooting

### Common Issues
1. **CORS Errors**: Update `api/index.php` headers
2. **Token Expiration**: Implement token refresh logic
3. **Network Timeouts**: Increase timeout values
4. **Image Loading**: Handle null image URLs gracefully

### Debug Mode
Enable debug logging in API:
```php
// In api/index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Support

- API Documentation: `api/README.php`
- Admin Panel: `admin/` directory
- Database Schema: `init_db_sqlite.sql`

Happy coding! 🚀