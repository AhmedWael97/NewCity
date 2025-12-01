<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="City Shop Directory API",
 *     version="1.0.0",
 *     description="API documentation for the City Shop Directory mobile application",
 *     @OA\Contact(
 *         email="admin@cityshops.com",
 *         name="API Support"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="phone", type="string", example="01234567890"),
 *     @OA\Property(property="avatar", type="string", nullable=true, example="http://example.com/storage/avatars/user.jpg"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="role", type="string", example="user"),
 *     @OA\Property(property="preferred_city_id", type="integer", nullable=true),
 *     @OA\Property(property="is_verified", type="boolean", example=false),
 *     @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
 *     @OA\Property(property="gender", type="string", enum={"male", "female"}, nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="City",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Cairo"),
 *     @OA\Property(property="name_ar", type="string", example="Cairo Arabic"),
 *     @OA\Property(property="slug", type="string", example="cairo"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="description_ar", type="string", nullable=true),
 *     @OA\Property(property="image", type="string", nullable=true),
 *     @OA\Property(property="banner_image", type="string", nullable=true),
 *     @OA\Property(property="latitude", type="number", format="float", example=30.0444),
 *     @OA\Property(property="longitude", type="number", format="float", example=31.2357),
 *     @OA\Property(property="population", type="integer", nullable=true),
 *     @OA\Property(property="area_km2", type="number", format="float", nullable=true),
 *     @OA\Property(property="is_capital", type="boolean", example=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="active_shops_count", type="integer", example=150)
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Restaurants"),
 *     @OA\Property(property="name_ar", type="string", example="Restaurants Arabic"),
 *     @OA\Property(property="slug", type="string", example="restaurants"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="description_ar", type="string", nullable=true),
 *     @OA\Property(property="icon", type="string", nullable=true),
 *     @OA\Property(property="color", type="string", example="#007bff"),
 *     @OA\Property(property="parent_id", type="integer", nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="sort_order", type="integer", example=0)
 * )
 * 
 * @OA\Schema(
 *     schema="Shop",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Pizza Palace"),
 *     @OA\Property(property="slug", type="string", example="pizza-palace"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="address", type="string", example="123 Main Street"),
 *     @OA\Property(property="phone", type="string", example="01234567890"),
 *     @OA\Property(property="whatsapp", type="string", nullable=true),
 *     @OA\Property(property="email", type="string", format="email", nullable=true),
 *     @OA\Property(property="website", type="string", nullable=true),
 *     @OA\Property(property="facebook", type="string", nullable=true),
 *     @OA\Property(property="instagram", type="string", nullable=true),
 *     @OA\Property(property="latitude", type="number", format="float"),
 *     @OA\Property(property="longitude", type="number", format="float"),
 *     @OA\Property(property="opening_hours", type="object", nullable=true),
 *     @OA\Property(property="is_open_now", type="boolean", example=true),
 *     @OA\Property(property="is_verified", type="boolean", example=false),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="average_rating", type="number", format="float", example=4.5),
 *     @OA\Property(property="total_ratings", type="integer", example=25),
 *     @OA\Property(property="total_views", type="integer", example=150),
 *     @OA\Property(property="images", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="logo", type="string", nullable=true),
 *     @OA\Property(property="banner_image", type="string", nullable=true),
 *     @OA\Property(property="delivery_available", type="boolean", example=true),
 *     @OA\Property(property="delivery_fee", type="number", format="float", nullable=true),
 *     @OA\Property(property="minimum_order", type="number", format="float", nullable=true),
 *     @OA\Property(property="distance_km", type="number", format="float", nullable=true)
 * )
 * 
 * @OA\Schema(
 *     schema="Rating",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
 *     @OA\Property(property="comment", type="string", nullable=true),
 *     @OA\Property(property="images", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="is_verified", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="ServiceCategory",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Plumbing"),
 *     @OA\Property(property="name_ar", type="string", example="Plumbing Arabic"),
 *     @OA\Property(property="slug", type="string", example="plumbing"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="description_ar", type="string", nullable=true),
 *     @OA\Property(property="icon", type="string", nullable=true),
 *     @OA\Property(property="color", type="string", example="#007bff"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="sort_order", type="integer", example=0)
 * )
 * 
 * @OA\Schema(
 *     schema="UserService",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Professional Plumbing Services"),
 *     @OA\Property(property="description", type="string", example="Expert plumbing services for homes and offices"),
 *     @OA\Property(property="pricing_type", type="string", enum={"fixed", "hourly", "per_km", "negotiable"}),
 *     @OA\Property(property="price_from", type="number", format="float", nullable=true),
 *     @OA\Property(property="price_to", type="number", format="float", nullable=true),
 *     @OA\Property(property="currency", type="string", example="EGP"),
 *     @OA\Property(property="images", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="phone", type="string", example="01234567890"),
 *     @OA\Property(property="whatsapp", type="string", nullable=true),
 *     @OA\Property(property="location", type="string", nullable=true),
 *     @OA\Property(property="address", type="string", nullable=true),
 *     @OA\Property(property="availability", type="object", nullable=true),
 *     @OA\Property(property="service_areas", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="requirements", type="string", nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="is_verified", type="boolean", example=false),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="rating", type="number", format="float", example=4.5),
 *     @OA\Property(property="total_reviews", type="integer", example=10),
 *     @OA\Property(property="total_views", type="integer", example=50),
 *     @OA\Property(property="total_contacts", type="integer", example=5)
 * )
 * 
 * @OA\Schema(
 *     schema="DeviceToken",
 *     type="object",
 *     description="Device Token model for push notifications",
 *     @OA\Property(property="id", type="integer", example=123),
 *     @OA\Property(property="user_id", type="integer", nullable=true, example=1, description="User ID (null for guest devices)"),
 *     @OA\Property(property="city_id", type="integer", nullable=true, example=1, description="City ID for targeted notifications"),
 *     @OA\Property(property="device_token", type="string", example="fcm_token_example", description="FCM token from Firebase"),
 *     @OA\Property(property="device_type", type="string", enum={"android", "ios", "web"}, example="android"),
 *     @OA\Property(property="device_name", type="string", example="Samsung Galaxy S21", nullable=true),
 *     @OA\Property(property="os_version", type="string", example="Android 13", nullable=true),
 *     @OA\Property(property="device_model", type="string", example="SM-G991B", nullable=true),
 *     @OA\Property(property="device_manufacturer", type="string", example="Samsung", nullable=true),
 *     @OA\Property(property="device_id", type="string", example="unique-device-id", nullable=true),
 *     @OA\Property(property="app_version", type="string", example="1.0.0", nullable=true),
 *     @OA\Property(property="app_build_number", type="string", example="100", nullable=true),
 *     @OA\Property(property="language", type="string", example="ar", default="ar"),
 *     @OA\Property(property="timezone", type="string", example="Asia/Riyadh", nullable=true),
 *     @OA\Property(property="ip_address", type="string", example="192.168.1.1", nullable=true),
 *     @OA\Property(property="device_metadata", type="object", nullable=true, description="Additional device metadata"),
 *     @OA\Property(property="is_active", type="boolean", example=true, default=true),
 *     @OA\Property(property="notifications_enabled", type="boolean", example=true, default=true),
 *     @OA\Property(property="last_used_at", type="string", format="date-time", example="2025-12-02T10:30:00.000000Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-01T08:15:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-01T08:15:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Operation successful"),
 *     @OA\Property(property="data", type="object", nullable=true)
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(property="errors", type="object")
 * )
 */
class ApiDocumentationController extends Controller
{
    // This controller exists only to hold Swagger documentation annotations
}