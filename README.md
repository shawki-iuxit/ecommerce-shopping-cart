# E-Commerce Shopping Cart - Technical Architecture & Implementation

## Overview for Technical Interview

This e-commerce application demonstrates modern web development practices using **Laravel 12**, **Vue 3**, **Inertia.js**, and **Domain-Driven Design (DDD)** principles. The architecture showcases clean separation of concerns, scalable patterns, and production-ready code organization.

---

## 🏗️ **Backend Architecture**

### **Domain-Driven Design Structure**

The application follows DDD principles with the business logic organized in the `app/Domain/` directory:

```
app/Domain/
├── Product/
│   ├── Services/ProductService.php           # Business logic layer
│   ├── Repositories/
│   │   ├── ProductRepositoryInterface.php    # Contract definition
│   │   └── EloquentProductRepository.php     # Database implementation
│   ├── DTOs/
│   │   ├── ProductListDTO.php               # Data transfer object
│   │   └── CategoryDTO.php                  # Category data structure
│   └── Transformers/ProductTransformer.php  # Model-to-DTO conversion
├── Cart/
│   ├── Services/CartService.php             # Cart business logic
│   ├── Repositories/CartRepositoryInterface.php
│   ├── DTOs/CartDTO.php & CartItemDTO.php
│   └── Transformers/CartTransformer.php
└── Order/
    └── Services/OrderService.php            # Order processing logic
```

### **Key Architectural Patterns**

#### **1. Service Layer Pattern**
- **ProductService** (`app/Domain/Product/Services/ProductService.php:11`)
  - Handles all product-related business logic
  - Orchestrates repository calls and data transformation

#### **2. Repository Pattern**
- **Interface-based contracts** ensure loose coupling
- **EloquentProductRepository** (`app/Domain/Product/Repositories/EloquentProductRepository.php:9`)
  - Implements database operations with eager loading

#### **3. Data Transfer Objects (DTOs)**
- **ProductListDTO** (`app/Domain/Product/DTOs/ProductListDTO.php:5`)
  - Immutable data structures using PHP 8 readonly properties
  - Type-safe data transfer between layers

#### **4. Transformer Pattern**
- **ProductTransformer** (`app/Domain/Product/Transformers/ProductTransformer.php:9`)
  - Converts Eloquent models to DTOs
  - Manages relationship loading and null safety

### **API Controller Architecture**

**ProductController** (`app/Http/Controllers/Api/ProductController.php:10`) demonstrates:
- **Dependency injection** of service layer
- **Structured JSON responses** with pagination metadata
- **Error handling** with environment-aware error messages
- **Input validation** and sanitization

---

## 🎨 **Frontend Architecture**

### **Vue 3 + Inertia.js SPA**

The frontend uses **Inertia.js v2** to create a single-page application experience without API complexity.

#### **Component Structure**
- **Pages**: `/resources/js/Pages/` - Full page components
- **UI Components**: `/resources/js/components/ui/` - Reusable shadcn/ui components
- **Layouts**: `/resources/js/layouts/` - 


## 🛠️ **Technical Implementation Highlights**

### **Modern PHP 8.3 Features**
- **Constructor property promotion** in DTOs and services
- **Readonly properties** for immutable data structures

### **Laravel 12 Best Practices**
- **Service Container** dependency injection
- **Eloquent relationships** with eager loading prevention of N+1
- **API Resources** for consistent JSON responses


### **Design Decisions Explained**

- **Repository Pattern**: Abstracts database logic for easier testing and potential DB changes
- **DTOs over Models**: Prevents over-fetching and provides API stability
- **Transformers**: Centralize business logic for data formatting and calculation
- **Inertia.js**: Provides SPA experience without API complexity and CORS issues


## 🚀 **Getting Started**

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Start development
composer run dev  # or npm run dev
```

## 📊 **Testing Strategy**

```bash
# Run backend tests
php artisan test

```

---

## 💡 **Summary**

**"This application demonstrates my ability to architect scalable web applications using modern frameworks and design patterns. The backend showcases Domain-Driven Design with clean separation between business logic, data access, and presentation layers. The frontend leverages Vue 3's Composition API with Inertia.js for seamless user experience. Key technical strengths include type safety, performance optimization, and production-ready error handling."**

---

*This architecture ensures maintainability, scalability, and developer experience while following industry best practices and modern web development standards.*