# GroceryPlus Android App - Configuration & Setup

## Project Structure

```
GroceryPlus/
├── app/
│   ├── src/
│   │   ├── main/
│   │   │   ├── java/com/example/groceryplus/
│   │   │   │   ├── data/
│   │   │   │   │   ├── api/
│   │   │   │   │   │   ├── GroceryPlusAPI.kt
│   │   │   │   │   │   └── RetrofitClient.kt
│   │   │   │   │   ├── models/
│   │   │   │   │   │   ├── User.kt
│   │   │   │   │   │   ├── Product.kt
│   │   │   │   │   │   ├── Order.kt
│   │   │   │   │   │   └── ApiResponse.kt
│   │   │   │   │   └── repository/
│   │   │   │   │       └── GroceryRepository.kt
│   │   │   │   ├── ui/
│   │   │   │   │   ├── screens/
│   │   │   │   │   │   ├── LoginScreen.kt
│   │   │   │   │   │   ├── RegisterScreen.kt
│   │   │   │   │   │   ├── ProductsScreen.kt
│   │   │   │   │   │   ├── CartScreen.kt
│   │   │   │   │   │   └── OrdersScreen.kt
│   │   │   │   │   └── viewmodel/
│   │   │   │   │       ├── LoginViewModel.kt
│   │   │   │   │       ├── ProductsViewModel.kt
│   │   │   │   │       └── CartViewModel.kt
│   │   │   │   └── MainActivity.kt
│   │   │   └── AndroidManifest.xml
│   │   └── test/
│   │       └── java/com/example/groceryplus/
│   │           └── GroceryRepositoryTest.kt
│   └── build.gradle
├── gradle.properties
└── README.md
```

---

## Build Configuration

### build.gradle (Project-level)

```gradle
buildscript {
    ext {
        compose_ui_version = '1.5.0'
        kotlin_version = '1.9.0'
    }
    repositories {
        google()
        mavenCentral()
    }
    dependencies {
        classpath 'com.android.tools.build:gradle:8.0.0'
        classpath "org.jetbrains.kotlin:kotlin-gradle-plugin:$kotlin_version"
    }
}

plugins {
    id 'com.android.application' version '8.0.0' apply false
    id 'com.android.library' version '8.0.0' apply false
    id 'org.jetbrains.kotlin.android' version '1.9.0' apply false
}
```

### build.gradle (App-level)

```gradle
plugins {
    id 'com.android.application'
    id 'kotlin-android'
    id 'kotlin-kapt'
}

android {
    namespace 'com.example.groceryplus'
    compileSdk 33

    defaultConfig {
        applicationId "com.example.groceryplus"
        minSdk 21
        targetSdk 33
        versionCode 1
        versionName "1.0.0"

        testInstrumentationRunner "androidx.test.runner.AndroidJUnitRunner"
        vectorDrawables.useSupportLibrary = true
    }

    buildTypes {
        release {
            minifyEnabled false
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }

    compileOptions {
        sourceCompatibility JavaVersion.VERSION_1_8
        targetCompatibility JavaVersion.VERSION_1_8
    }

    kotlinOptions {
        jvmTarget = '1.8'
    }

    buildFeatures {
        compose true
    }

    composeOptions {
        kotlinCompilerExtensionVersion '1.5.0'
    }

    packagingOptions {
        resources {
            excludes += '/META-INF/{AL2.0,LGPL2.1}'
        }
    }
}

dependencies {
    // Core Android
    implementation 'androidx.core:core-ktx:1.10.1'
    implementation 'androidx.lifecycle:lifecycle-runtime-ktx:2.6.1'
    implementation 'androidx.activity:activity-compose:1.7.2'

    // Jetpack Compose
    implementation platform('androidx.compose:compose-bom:2023.09.00')
    implementation 'androidx.compose.ui:ui'
    implementation 'androidx.compose.ui:ui-graphics'
    implementation 'androidx.compose.ui:ui-tooling-preview'
    implementation 'androidx.compose.material3:material3:1.0.1'
    debugImplementation 'androidx.compose.ui:ui-tooling'
    debugImplementation 'androidx.compose.ui:ui-test-manifest'

    // Retrofit & OkHttp
    implementation 'com.squareup.retrofit2:retrofit:2.9.0'
    implementation 'com.squareup.retrofit2:converter-gson:2.9.0'
    implementation 'com.squareup.okhttp3:okhttp:4.10.0'
    implementation 'com.squareup.okhttp3:logging-interceptor:4.10.0'

    // Coroutines
    implementation 'org.jetbrains.kotlinx:kotlinx-coroutines-android:1.7.1'
    implementation 'org.jetbrains.kotlinx:kotlinx-coroutines-core:1.7.1'

    // ViewModel & LiveData
    implementation 'androidx.lifecycle:lifecycle-viewmodel-ktx:2.6.1'
    implementation 'androidx.lifecycle:lifecycle-livedata-ktx:2.6.1'

    // Room Database
    implementation 'androidx.room:room-runtime:2.5.2'
    kapt 'androidx.room:room-compiler:2.5.2'
    implementation 'androidx.room:room-ktx:2.5.2'

    // DataStore
    implementation 'androidx.datastore:datastore-preferences:1.0.0'

    // Navigation
    implementation 'androidx.navigation:navigation-compose:2.6.0'

    // Gson
    implementation 'com.google.code.gson:gson:2.10.1'

    // Image Loading
    implementation 'com.github.bumptech.glide:glide:4.15.1'
    implementation 'com.github.bumptech.glide:okhttp3-integration:4.15.1'
    kapt 'com.github.bumptech.glide:compiler:4.15.1'

    // Coil (Alternative to Glide)
    implementation 'io.coil-kt:coil-compose:2.4.0'

    // Accompanist
    implementation 'com.google.accompanist:accompanist-pager:0.30.0'

    // Testing
    testImplementation 'junit:junit:4.13.2'
    testImplementation 'io.mockk:mockk:1.13.4'
    testImplementation 'org.jetbrains.kotlinx:kotlinx-coroutines-test:1.7.1'
    
    androidTestImplementation 'androidx.test.ext:junit:1.1.5'
    androidTestImplementation 'androidx.test.espresso:espresso-core:3.5.1'
    androidTestImplementation platform('androidx.compose:compose-bom:2023.09.00')
    androidTestImplementation 'androidx.compose.ui:ui-test-junit4'
}
```

---

## AndroidManifest.xml

```xml
<?xml version="1.0" encoding="utf-8"?>
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:tools="http://schemas.android.com/tools">

    <!-- Internet Permission -->
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />

    <!-- File Access -->
    <uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
    <uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />

    <!-- Camera for image capture -->
    <uses-permission android:name="android.permission.CAMERA" />

    <!-- Location (if implementing delivery tracking) -->
    <uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
    <uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />

    <application
        android:allowBackup="true"
        android:dataExtractionRules="@xml/data_extraction_rules"
        android:fullBackupContent="@xml/backup_rules"
        android:icon="@mipmap/ic_launcher"
        android:label="@string/app_name"
        android:supportsRtl="true"
        android:theme="@style/Theme.GroceryPlus"
        tools:targetApi="31">

        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:theme="@style/Theme.GroceryPlus">
            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>
        </activity>

    </application>

</manifest>
```

---

## Configuration File Structure

### local.properties (Not committed to git)
```properties
sdk.dir=/path/to/android/sdk
```

### gradle.properties
```properties
# Project-wide Gradle settings
org.gradle.jvmargs=-Xmx2048m -XX:MaxPermSize=512m
android.useAndroidX=true
android.enableJetifier=true

# API Configuration
API_BASE_URL=http://10.0.2.2/groceryplus/api/
API_TIMEOUT_SECONDS=30

# Build Configuration
COMPILE_SDK=33
TARGET_SDK=33
MIN_SDK=21
```

---

## Environment Configuration

### strings.xml (Development)
```xml
<?xml version="1.0" encoding="utf-8"?>
<resources>
    <string name="app_name">GroceryPlus</string>
    <string name="api_base_url">http://10.0.2.2/groceryplus/api/</string>
    <string name="app_base_url">http://10.0.2.2/groceryplus/</string>
    <string name="api_timeout">30</string>
    <string name="debug_mode">true</string>
</resources>
```

### strings.xml (Production)
```xml
<?xml version="1.0" encoding="utf-8"?>
<resources>
    <string name="app_name">GroceryPlus</string>
    <string name="api_base_url">https://api.groceryplus.com/api/</string>
    <string name="app_base_url">https://groceryplus.com/</string>
    <string name="api_timeout">30</string>
    <string name="debug_mode">false</string>
</resources>
```

---

## API URL Configuration

### For Emulator
```kotlin
// IP address to access host machine from Android emulator
const val API_BASE = "http://10.0.2.2/groceryplus/api/"
```

### For Physical Device (Local Network)
```kotlin
// Replace with your computer's local IP
const val API_BASE = "http://192.168.1.100/groceryplus/api/"
```

### For Production
```kotlin
const val API_BASE = "https://api.groceryplus.com/api/"
```

---

## Kotlin Constants File

**data/api/Constants.kt**
```kotlin
object Constants {
    // API Configuration
    const val API_BASE_URL = "http://10.0.2.2/groceryplus/api/"
    const val CONNECT_TIMEOUT = 30L
    const val READ_TIMEOUT = 30L
    const val WRITE_TIMEOUT = 30L

    // SharedPreferences Keys
    const val PREF_NAME = "groceryplus_prefs"
    const val KEY_AUTH_TOKEN = "auth_token"
    const val KEY_USER_ID = "user_id"
    const val KEY_USER_EMAIL = "user_email"
    const val KEY_USER_NAME = "user_name"

    // DataStore Keys
    const val DATASTORE_NAME = "app_settings"

    // Default Values
    const val DEFAULT_PAGE_SIZE = 50
    const val MAX_PAGE_SIZE = 100

    // Time Constants
    const val TOKEN_EXPIRY_HOURS = 168 // 7 days

    // UI Constants
    const val ANIMATION_DURATION = 300
    const val LOADING_TIMEOUT = 30000L // 30 seconds

    // File Upload
    const val MAX_FILE_SIZE = 5 * 1024 * 1024 // 5MB
    const val ALLOWED_IMAGE_TYPES = "image/jpeg,image/png,image/gif,image/webp"
}
```

---

## Network Configuration

**data/api/NetworkConfig.kt**
```kotlin
object NetworkConfig {
    fun createOkHttpClient(): OkHttpClient {
        val loggingInterceptor = HttpLoggingInterceptor().apply {
            level = if (BuildConfig.DEBUG) {
                HttpLoggingInterceptor.Level.BODY
            } else {
                HttpLoggingInterceptor.Level.NONE
            }
        }

        return OkHttpClient.Builder()
            .addInterceptor(loggingInterceptor)
            .connectTimeout(Constants.CONNECT_TIMEOUT, TimeUnit.SECONDS)
            .readTimeout(Constants.READ_TIMEOUT, TimeUnit.SECONDS)
            .writeTimeout(Constants.WRITE_TIMEOUT, TimeUnit.SECONDS)
            .addNetworkInterceptor { chain ->
                // Add custom headers
                val originalRequest = chain.request()
                val requestBuilder = originalRequest.newBuilder()

                // Add Authorization header if token exists
                val token = // Get from SharedPreferences or DataStore
                if (token != null) {
                    requestBuilder.header("Authorization", "Bearer $token")
                }

                requestBuilder.header("Content-Type", "application/json")
                chain.proceed(requestBuilder.build())
            }
            .build()
    }

    fun createRetrofit(okHttpClient: OkHttpClient): Retrofit {
        return Retrofit.Builder()
            .baseUrl(Constants.API_BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }
}
```

---

## Dependency Injection Setup

**Using Manual DI (Simple approach)**
```kotlin
object ServiceLocator {
    private var apiService: GroceryPlusAPI? = null
    private var repository: GroceryRepository? = null

    fun getApiService(): GroceryPlusAPI {
        return apiService ?: run {
            val okHttpClient = NetworkConfig.createOkHttpClient()
            val retrofit = NetworkConfig.createRetrofit(okHttpClient)
            retrofit.create(GroceryPlusAPI::class.java)
                .also { apiService = it }
        }
    }

    fun getRepository(context: Context): GroceryRepository {
        return repository ?: run {
            GroceryRepository(
                api = getApiService(),
                prefs = DataStore(context)
            ).also { repository = it }
        }
    }
}
```

**Using Hilt (Advanced approach)**
```kotlin
@Module
@InstallIn(SingletonComponent::class)
object NetworkModule {
    
    @Provides
    @Singleton
    fun provideOkHttpClient(): OkHttpClient {
        return NetworkConfig.createOkHttpClient()
    }

    @Provides
    @Singleton
    fun provideRetrofit(okHttpClient: OkHttpClient): Retrofit {
        return NetworkConfig.createRetrofit(okHttpClient)
    }

    @Provides
    @Singleton
    fun provideGroceryPlusAPI(retrofit: Retrofit): GroceryPlusAPI {
        return retrofit.create(GroceryPlusAPI::class.java)
    }

    @Provides
    @Singleton
    fun provideRepository(
        api: GroceryPlusAPI,
        @ApplicationContext context: Context
    ): GroceryRepository {
        return GroceryRepository(api, DataStore(context))
    }
}
```

---

## ProGuard Rules

**proguard-rules.pro**
```proguard
# Retrofit
-keep class retrofit2.** { *; }
-keepattributes Signature
-keepattributes Exceptions

# OkHttp
-dontwarn okhttp3.**
-dontwarn okio.**

# Gson
-keep class com.google.gson.** { *; }
-keepclassmembers class * {
    @com.google.gson.annotations.SerializedName <fields>;
}

# Models
-keep class com.example.groceryplus.data.models.** { *; }
-keep class com.example.groceryplus.data.api.** { *; }

# Coroutines
-keepclassmembernames class kotlinx.** {
    volatile <fields>;
}

# Keep Composable functions
-keepclasseswithmembernames class * {
    @androidx.compose.runtime.Composable <methods>;
}
```

---

## Testing Configuration

**build.gradle additions**
```gradle
android {
    testOptions {
        unitTests {
            includeAndroidResources = true
        }
    }
}
```

**Test Dependencies**
```gradle
testImplementation 'junit:junit:4.13.2'
testImplementation 'io.mockk:mockk:1.13.4'
testImplementation 'org.jetbrains.kotlinx:kotlinx-coroutines-test:1.7.1'
testImplementation 'androidx.room:room-testing:2.5.2'

androidTestImplementation 'androidx.test.ext:junit:1.1.5'
androidTestImplementation 'androidx.test.espresso:espresso-core:3.5.1'
androidTestImplementation 'androidx.compose.ui:ui-test-junit4'
```

---

## Build Variants

**build.gradle**
```gradle
android {
    flavorDimensions "environment"
    
    productFlavors {
        dev {
            dimension "environment"
            applicationIdSuffix ".dev"
            versionNameSuffix "-dev"
            buildConfigField "String", "API_URL", '"http://10.0.2.2/groceryplus/api/"'
            buildConfigField "boolean", "DEBUG_LOGS", "true"
        }
        
        staging {
            dimension "environment"
            applicationIdSuffix ".staging"
            versionNameSuffix "-staging"
            buildConfigField "String", "API_URL", '"https://staging-api.groceryplus.com/api/"'
            buildConfigField "boolean", "DEBUG_LOGS", "true"
        }
        
        prod {
            dimension "environment"
            buildConfigField "String", "API_URL", '"https://api.groceryplus.com/api/"'
            buildConfigField "boolean", "DEBUG_LOGS", "false"
        }
    }
}
```

---

## Running the App

### From Android Studio
1. File → Open → Select project folder
2. Wait for Gradle sync
3. Select device/emulator
4. Click Run (▶️)

### From Command Line
```bash
# Build APK
./gradlew build

# Build and run on emulator
./gradlew installDebug
adb shell am start -n com.example.groceryplus/.MainActivity

# Run tests
./gradlew test
./gradlew connectedAndroidTest
```

---

## Troubleshooting

### API Connection Issues
```kotlin
// Increase timeout for slow networks
const val CONNECT_TIMEOUT = 60L
const val READ_TIMEOUT = 60L

// Add retry interceptor
.addInterceptor { chain ->
    var request = chain.request()
    var response: Response? = null
    var exception: Exception? = null

    for (i in 0..2) { // 3 retries
        try {
            response = chain.proceed(request)
            if (response.isSuccessful) return@addInterceptor response
        } catch (e: Exception) {
            exception = e
        }
    }
    response ?: throw exception ?: IOException("Failed after 3 retries")
}
```

---

## Next Steps

1. Clone/setup Android project with this structure
2. Update API_BASE_URL for your environment
3. Run `./gradlew build` to compile
4. Run `php api/api_test.php` to verify API
5. Start app and test login flow
6. Implement screens one by one
7. Test thoroughly before release

---

**Happy coding!** 🚀
