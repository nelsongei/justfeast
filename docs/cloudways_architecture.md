# High-Concurrency Cloudways Infrastructure Architecture Guide

## Overview & Stadium Traffic Pattern

Concert & stadium traffic arrives in sharp, extreme burst spikes:
1. **Pre-Show (30 mins before start)**: Massive surges in ticket validation & initial vendor food/drink orders.
2. **Intermission / Performance Breaks**: Concentrated spikes of 5,000–20,000 simultaneous order requests in 10-minute windows.
3. **Post-Show**: Final spike in quick takeaway orders and runner delivery completions.

This architecture guide details the production deployment layout on **Cloudways** to sustain high concurrency, zero downtime, and instant responsiveness.

---

## 🏛️ Cloudways Infrastructure Architecture Topology

```
                  ┌────────────────────────┐
                  │ Cloudflare WAF / CDN   │ (DDoS Protection, SSL, Static Assets)
                  └───────────┬────────────┘
                              │
                  ┌───────────▼────────────┐
                  │ Cloudways Nginx / HA   │ (Load Balancer & Web Application Firewall)
                  └─────┬────────────┬─────┘
                        │            │
         ┌──────────────▼───┐    ┌───▼──────────────┐
         │ App Node 1 (PHP) │    │ App Node 2 (PHP) │ (Laravel 10 App Nodes)
         └──────┬───────────┘    └───┬──────────────┘
                │                    │
 ┌──────────────┴────────────────────┴──────────────┐
 │ Managed Redis Server (Cloudways Redis Add-on)     │
 │ - Cache Driver (`CACHE_DRIVER=redis`)            │
 │ - Session Driver (`SESSION_DRIVER=redis`)        │
 │ - Queue Driver (`QUEUE_CONNECTION=redis`)        │
 │ - Rate Limiter Cache                             │
 └──────────────┬────────────────────┬──────────────┘
                │                    │
 ┌──────────────▼──────────┐  ┌──────▼─────────────────────────┐
 │ Managed MySQL Database  │  │ Dedicated Horizon Worker Node   │
 │ (Read Replicas & Pool)  │  │ (Separate Queue Processes)      │
 └─────────────────────────┘  └─────────────────────────────────┘
```

---

## ⚙️ Core Components Configuration

### 1. Redis Configuration (`.env`)
Cloudways includes native Redis management. Configure all transient state to Redis:

```ini
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your-cloudways-redis-password
REDIS_PORT=6379
```

### 2. Dedicated Laravel Horizon Queue Workers
To prevent queue jobs (payment webhook processing, SMS OTP notifications, runner allocation) from competing with web app HTTP processes, run Laravel Horizon on a dedicated worker instance:

```bash
# Managed via Cloudways Supervisor Daemon
php artisan horizon
```

### 3. Asset & Product Image Delivery (Cloudways CDN)
Since S3 is not used, static product images and asset files are stored on the Cloudways local public storage disk (`storage/app/public`) and served via **Cloudways CDN / Cloudflare Enterprise**:

```ini
APP_URL=https://justfeast.com
ASSET_URL=https://cdn.justfeast.com
```

### 4. Exponential Backoff Payment Polling & WebSockets
To prevent 10,000 simultaneous clients from flooding the API with 5,000 req/sec payment status requests:
- **WebSocket Broadcasts**: Order payment updates dispatch `PaymentStatusUpdated` event on channel `payment.updated.{order_id}`.
- **Client Exponential Backoff**: API endpoint `/api/orders/{order}/payment-status` enforces client backoff sequence:
  $$\text{Interval} = [2\text{s} \to 3\text{s} \to 5\text{s} \to 8\text{s} \to 15\text{s} \to 30\text{s}]$$

### 5. Automated Scaling & Event Window Preparation
1. **Pre-Event Ramp-up**: 2 hours before concert start, scale Cloudways server RAM/CPU from 4GB/2-core to 16GB/8-core or enable Cloudways Autonomous Auto-Scaling.
2. **OpCache Optimization**:
   ```ini
   opcache.enable=1
   opcache.memory_consumption=256
   opcache.max_accelerated_files=20000
   opcache.validate_timestamps=0
   ```
3. **Database Performance Indexing**: All mandatory composite indexes (`vendor_id, payment_status, created_at`, `user_id, order_status, created_at`, `runner_id, status, created_at`) are applied.
