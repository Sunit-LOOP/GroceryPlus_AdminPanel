I'm ready to proceed with generating the API client code for your Android app in Java. We'll follow the structure outlined in the `ANDROID_INTEGRATION_GUIDE.md`, adapting it for Java.

First, let's create the data models that represent the responses from your API. These will be Java POJO (Plain Old Java Objects).

**1. Create a `User.java` file**

Create a new Java class file named `User.java` in your Android project (e.g., in a `data.models` package). This class will represent a user object:

```java
package com.yourcompany.yourapp.data.models; // Adjust package name as needed

import com.google.gson.annotations.SerializedName;

public class User {
    @SerializedName("id")
    private int id;
    @SerializedName("name")
    private String name;
    @SerializedName("email")
    private String email;
    @SerializedName("phone")
    private String phone;
    @SerializedName("type")
    private String type;

    // Getters and Setters

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }
}
```

**2. Create an `AuthResponse.java` file**

Create another Java class file named `AuthResponse.java` in the same `data.models` package. This will hold the user data and the authentication token received after login or registration:

```java
package com.yourcompany.yourapp.data.models; // Adjust package name as needed

import com.google.gson.annotations.SerializedName;

public class AuthResponse {
    @SerializedName("user")
    private User user;
    @SerializedName("token")
    private String token;

    // Getters and Setters

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }
}
```

**3. Create an `ApiResponse.java` file**

This class will represent the standard response format for most API calls:

```java
package com.yourcompany.yourapp.data.models; // Adjust package name as needed

import com.google.gson.annotations.SerializedName;

public class ApiResponse<T> {
    @SerializedName("success")
    private boolean success;
    @SerializedName("status")
    private int status;
    @SerializedName("data")
    private T data;
    @SerializedName("message")
    private String message;
    @SerializedName("timestamp")
    private String timestamp;

    // Getters and Setters

    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public int getStatus() {
        return status;
    }

    public void setStatus(int status) {
        this.status = status;
    }

    public T getData() {
        return data;
    }

    public void setData(T data) {
        this.data = data;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(String timestamp) {
        this.timestamp = timestamp;
    }
}
```

Please create these three Java classes in your Android project, adjusting the package names as necessary. Let me know once you have created them, and we can move on to the next set of models.