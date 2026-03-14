# Installation & Requirements

1. **Composer dependencies**  
   Ensure `musonza/chat` is available: `composer require musonza/chat`. The repository already ships module code under `Modules\Chatly`.

2. **Service provider registration**  
   Confirm `Modules\Chatly\Providers\ChatlyServiceProvider::class` is listed in `config/app.php` so configuration is merged and routes can be registered when enabled.

3. **Publish & run migrations**  
   Execute `php artisan vendor:publish --provider="Musonza\Chat\ChatServiceProvider"` and `php artisan migrate` to create the required `chat_*` tables.

4. **Override configuration (optional)**  
   Copy `modules/chatly/config/chatly.php` to `config/chatly.php` if you need custom prefixes, middleware stacks, pagination limits, or to enable broadcasting.

5. **Permission setup**  
   Grant the `Permissions::CAN_ACCESS_CHAT` permission to the roles that should see the chat UI and API. Ensure conversation/message policies are registered in `App\Providers\AuthServiceProvider`.

6. **Front-end build config**  
   The Vite alias `'@Chatly'` points to `modules/chatly/resources/js`. Update it if paths change and rebuild assets with `npm run dev` or `npm run build`.

7. **Broadcast configuration (optional)**  
   Flip `chatly.broadcasts` to `true` and configure broadcasting drivers (Pusher, Ably, etc.) if you need real-time message events.
