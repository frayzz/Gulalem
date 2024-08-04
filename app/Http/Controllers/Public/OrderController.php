<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PhoneVerification;
use App\Models\Product;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $mobizon;

    public function __construct()
    {
        $this->mobizon = new Client([
            'base_uri' => 'https://api.mobizon.kz/',
        ]);
    }

    public function index($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();;
        return view('pages.order', compact('product'));
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'user_phone' => 'required|string',
        ]);

        $phone = preg_replace('/\D/', '', $request->user_phone);
        if (strlen($phone) == 10) {
            $phone = '7' . $phone;
        }

        $recentAttempt = PhoneVerification::where('phone', $phone)
            ->where('created_at', '>', Carbon::now()->subMinutes(10))
            ->count();

        if ($recentAttempt >= 3) {
            return response()->json(['message' => 'Превышено количество попыток отправки кода. Попробуйте позже.'], 429);
        }

        $code = rand(1000, 9999);

        if (app()->environment('local')) {
            Log::info("Verification code for {$phone}: {$code}");
        } else {
            try {
                $response = $this->mobizon->post('service/message/sendSmsMessage', [
                    'query' => [
                        'apiKey' => config('mobizon.api_key'),
                        'recipient' => $phone,
                        'text' => 'Gulalem.kz: ' . $code . ' ваш код подтверждения',
                    ],
                ]);

                $responseBody = json_decode($response->getBody(), true);

                if ($responseBody['code'] !== 0) {
                    Log::error('Mobizon SMS send error: ' . $responseBody['message']);
                    return response()->json(['message' => 'Ошибка отправки SMS.'], 500);
                }
            } catch (\Exception $e) {
                Log::error('Mobizon SMS send exception: ' . $e->getMessage());
                return response()->json(['message' => 'Ошибка отправки SMS.'], 500);
            }
        }

        PhoneVerification::updateOrCreate(
            ['phone' => $phone],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        return response()->json(['message' => 'Код отправлен.'], 200);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|integer',
            'user_phone' => 'required|string',
            'recipient_phone' => 'nullable|string',
            'address' => 'required|string',
            'selected_time' => 'required|string',
            'custom_time' => 'nullable|date',
            'card_message' => 'nullable|string',
            'payment_method' => 'required|string',
            'product_id' => 'required|integer|exists:products,id',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
        ]);

        // Преобразование номера телефона в формат без символов
        $phone = preg_replace('/\D/', '', $request->user_phone);
        if (strlen($phone) == 10) {
            $phone = '7' . $phone;
        }

        // Ключи для кеша попыток верификации и блокировки
        $attemptsKey = 'verification_attempts_' . $phone;
        $blockedKey = 'verification_blocked_' . $phone;

        // Увеличение количества попыток
        $attempts = Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, now()->addMinutes(10)); // Хранение количества попыток в течение 10 минут

        // Если количество попыток превышает лимит, блокируем номер на 10 минут
        if ($attempts > 5) {
            Cache::put($blockedKey, true, now()->addMinutes(10)); // Блокировка на 10 минут
            return response()->json(['message' => 'Превышено количество попыток. Попробуйте позже.'], 429);
        }

        $verification = PhoneVerification::where('phone', $phone)
            ->where('code', $request->verification_code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($verification) {
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => 'User-' . $phone,
                    'password' => bcrypt('temporary_password')
                ]
            );
            Auth::login($user);

            // Передача трех аргументов в метод createOrder
            $this->createOrder($request, $phone, $request->product_id);

            // Удаление записи верификации после успешной проверки
            $verification->delete();

            return response()->json(['message' => 'Заказ успешно оформлен!'], 200);
        } else {
            return response()->json(['message' => 'Неверный код подтверждения или код истек.'], 400);
        }
    }

    private function createOrder($request, $phone, $productId)
    {
        $order = new Order();
        $order->user_phone = $phone;
        $order->recipient_phone = $request->recipient_phone;
        $order->city = $request->city;
        $order->region = $request->region;
        $order->address = $request->address;
        $order->selected_time = $request->selected_time;
        $order->custom_time = $request->custom_time;
        $order->card_message = $request->card_message;
        $order->payment_method = $request->payment_method;
        $order->status = 'not-pay';
        $order->user_id = Auth::id();
        $order->product_id = $productId; // Заполнение поля product_id

        $order->save();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_phone' => 'required|string',
            'recipient_phone' => 'nullable|string',
            'address' => 'required|string',
            'selected_time' => 'required|string',
            'custom_time' => 'nullable|date',
            'card_message' => 'nullable|string',
            'payment_method' => 'required|string',
            'product_id' => 'required|integer|exists:products,id',
        ]);

        if (Auth::check()) {
            // Передача трех аргументов в метод createOrder
            $this->createOrder($request, Auth::user()->phone, $request->product_id);
            return redirect()->back()->with('success', 'Заказ успешно оформлен!');
        } else {
            return $this->sendVerificationCode($request);
        }
    }
}
