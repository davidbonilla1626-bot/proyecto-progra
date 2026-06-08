-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-06-2026 a las 04:08:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `restaurante1_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon_path`, `created_at`, `updated_at`) VALUES
(1, 'Hamburguesas', NULL, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(2, 'Hot Dogs', NULL, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(3, 'Pollo', NULL, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(4, 'Ensaladas', NULL, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(5, 'Acompañamientos', NULL, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(6, 'Bebidas', NULL, '2026-06-07 09:33:20', '2026-06-07 09:33:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_02_073418_create_categories_table', 1),
(5, '2026_05_02_193641_create_products_table', 1),
(6, '2026_05_28_000000_create_orders_table', 1),
(7, '2026_05_28_000001_create_order_items_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pendiente',
  `total` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `status`, `total`, `notes`, `created_at`, `updated_at`) VALUES
(1, 5, 'ORD-2026-001', 'Entregado', 5.99, 'Cliente: DAOZ | Teléfono: 48954988 | Dirección: mi casa | Instrucciones: tocar el timbre', '2026-06-07 09:48:34', '2026-06-07 09:48:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 17, 1, 2.50, '2026-06-07 09:48:34', '2026-06-07 09:48:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `image`, `stock`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hamburguesa Gran Megabyte', 'La clásica de la casa: doble carne de res, queso cheddar fundido.', 14.99, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500', 12, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(2, 1, 'Cyber-Bacon Pro Max', 'Hamburguesa premium con tiras de bacon crujiente.', 15.50, 'https://images.unsplash.com/photo-1585238341267-1cfec2046a55?w=500', 9, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(3, 1, 'Hamburguesa Glitch Veggie', 'Medallón de garbanzos, aguacate fresco y hummus.', 13.00, 'https://images.unsplash.com/photo-1512152272829-e3139592d56f?w=500', 9, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(4, 1, 'La Torre Superusuario (Root)', 'Triple carne, huevo frito, aros de cebolla.', 18.00, 'https://images.unsplash.com/photo-1596662951482-0c4ba74a6df6?w=500', 19, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(5, 2, 'Hot Dog Supersónico 5G', 'Salchicha jumbo de 30cm y relish especial.', 9.50, 'https://images.unsplash.com/photo-1612392062631-94dd858cba88?w=500', 22, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(6, 2, 'Nitro Chilli Dog', 'Chilli con carne picante y jalapeños.', 11.00, 'https://images.unsplash.com/photo-1619740455993-9e47519a8844?w=500', 21, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(7, 3, 'Alitas Terabyte BBQ', '10 alitas bañadas en BBQ coreana ahumada.', 12.99, 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=500', 22, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(8, 3, 'Sándwich Infinite Loop', 'Pollo frito extra crujiente y mayonesa picante.', 13.50, 'https://images.unsplash.com/photo-1606755962773-53240004f14a?w=500', 8, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(9, 4, 'Ensalada Clean Code', 'Mix de verdes orgánicos, quinoa y arándanos.', 10.99, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500', 13, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(10, 5, 'Papas Overclocked', 'Papas fritas con queso fundido y bacon.', 6.99, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=500', 21, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(11, 5, 'Nuggets Cuánticos', '6 piezas de pollo crujiente.', 7.50, 'https://images.unsplash.com/photo-1562967914-608f82629710?w=500', 17, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(12, 5, 'Aros de Token Ring', 'Aros de cebolla tempurizados circulares.', 5.50, 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=500', 12, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(13, 6, 'Turbo Batido Choco-Script', 'Chocolate belga con trozos de brownie.', 7.25, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500', 12, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(14, 6, 'Soda Azul Eléctrico', 'Infusión de arándano azul burbujeante.', 4.50, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500', 16, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(15, 6, 'Cola Clásica Legacy', 'La receta de siempre.', 3.00, 'https://images.unsplash.com/photo-1581006852262-e4307cf6283a?w=500', 10, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(16, 6, 'Nitro Cold Brew', 'Café extraído en frío con nitrógeno.', 6.00, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500', 5, '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(17, 6, 'Frappe', 'Choclate', 2.50, 'https://www.callebaut.com/sites/default/files/styles/half_width_image/public/FrozenDrinkDark_4972600x870_1.jpg.webp?itok=cRaFxpP2', 99, '2026-06-07 09:46:52', '2026-06-07 09:48:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2CXUUO0SQXYk2u7UjzVe857al8X2gzRikRtekvXO', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.123.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM3RBV1R1R0daRDNhUjIyVmlzRTlBMUJ3cWJscEQ3QU1lc1Nxd0JYdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tZW51IjtzOjU6InJvdXRlIjtzOjExOiJwdWJsaWMubWVudSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7fQ==', 1780876619),
('ckqLjc3ZSpoDl2XC4czU85iOjxiu6ofzo06F6KZQ', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTW45aDdZZWRPek1XaWNGd3RkV3hDS2xJY01Ub1VGbENIU0R3TjhIZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tZW51IjtzOjU6InJvdXRlIjtzOjExOiJwdWJsaWMubWVudSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7fQ==', 1780875488);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'David Bonilla', 'davidbonilla1626@gmail.com', '2026-06-07 09:33:19', '$2y$12$FKW6SFBigq/dNkKIVkjbT.IPBIVl9Xl4.6KXH5aDdcmPT6GlZW.7y', 'user', 'nPkThoro4c', '2026-06-07 09:33:19', '2026-06-07 09:33:19'),
(2, 'Administrador QuickBite', 'admin@quickbite.com', '2026-06-07 09:33:20', '$2y$12$7W7gK9X8J2KYr2BkFzMzMO4eZGsyoxwKt8MV0EzQz0nnUSNsWxmGO', 'admin', 'rCJ6jdl3oZ', '2026-06-07 09:33:20', '2026-06-07 09:33:20'),
(3, 'D4niel', 'orellanadaniel461@gmail.com', NULL, '$2y$12$qVLXbslkJJ6aXN2IDe4sCusxeY6CHzAwZoL/RduW5FHNL4NUn07qa', 'user', NULL, '2026-06-07 09:37:14', '2026-06-07 09:37:14'),
(5, 'DAOZ', 'daoz20403@gmail.com', NULL, '$2y$12$r6o5u7398nZP.zk4gv6.xOGnLdLFC04dMFyS9ZFKHDPn8Ts3LDAPa', 'admin', 'lyjY6E0CBy8EAosAbiWPvAUjKLusX77xlsi0OTLSU7XOQrsdwy1ZZkk9iAZp', '2026-06-07 09:45:12', '2026-06-07 09:45:12');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
