<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *     title="Role and Permission Management API",
 *     version="1.0.0",
 *     description="API для управления ролями и привилегиями"
 * )
 *
 * @OA\Tag(name="Auth", description="API для аутентификации")
 */
class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/send-sms",
     *     tags={"Auth"},
     *     summary="Получение смс для верификации. Если телефон не зарегестрирован в системе - он зарегестрируется и все равно получит СМС.",
     *     description="Отправляет SMS с кодом подтверждения на указанный номер телефона.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"phone"},
     *
     *             @OA\Property(property="phone", type="string", example="9001234567")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="SMS отправлено", @OA\JsonContent(
     *
     *         @OA\Property(property="message", type="string", example="Код подтверждения отправлен.")
     *     )),
     *
     *     @OA\Response(response=400, description="Ошибка отправки SMS")
     * )
     */
    public function sendSms(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $phone = $this->formatPhoneNumber($request->input('phone'));
        $code = rand(100000, 999999);

        // Создание или обновление пользователя
        $user = User::firstOrCreate(['phone' => $phone], [
            'password' => Hash::make(Str::random(8)),
        ]);

        $user->update([
            'sms_code' => $code,
            'sms_code_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Отправка SMS через сервис
        $smsSent = $this->sendSmsThroughVernoInfo($phone, $code);

        if (!$smsSent) {
            return response()->json(['message' => 'Ошибка отправки SMS'], 400);
        }

        return response()->json(['message' => 'Код подтверждения отправлен.'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/verify-sms",
     *     tags={"Auth"},
     *     summary="Подтверждение кода и авторизация",
     *     description="Проверяет код из SMS и выдаёт токен пользователю.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"phone", "code"},
     *
     *             @OA\Property(property="phone", type="string", example="9001234567"),
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Успешная авторизация",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="phone", type="string", example="+79001234567")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=400, description="Неверный код или срок действия истёк")
     * )
     */
    public function verifySms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $phone = $this->formatPhoneNumber($request->input('phone'));
        $inputCode = $request->input('code');

        $user = User::where('phone', $phone)
            ->where('sms_code', $inputCode)
            ->where('sms_code_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Неверный код или срок действия истёк'], 400);
        }

        // Очистка кода после успешной верификации
        $user->update([
            'sms_code' => null,
            'sms_code_expires_at' => null,
        ]);

        // Генерация токена через Passport
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'phone' => $user->phone,
            ],
        ], 200);
    }

    /**
     * Отправляет SMS через сервис.
     */
    private function sendSmsThroughVernoInfo(string $phone, int $code): bool
    {
        $smsApiUrl = config('services.sms.url');
        $smsLogin = config('services.sms.login');
        $smsPassword = config('services.sms.password');

        // $message = "Ваш код подтверждения: $code";

        try {
            $response = Http::post('https://lk.rapporto.ru/vernoinfo_rest', [
                'login' => 'vernoinfo_rest',
                'password' => 'Dgb41VaN',
                'destAddr' => $phone,
                'message' => [
                    'type' => 'SMS',
                    'data' => [
                        'text' => "Ваш код подтверждения: $code",
                        'serviceNumber' => 'Vernyi',
                        'ttl' => 10,
                    ],
                ],
            ]);

            // Логируем тело ответа
            Log::info('VernoInfo API Response', ['response' => $response->body()]);

            // Проверяем успешность и парсим JSON
            $responseData = $response->json();
            if ($response->successful() && isset($responseData['mtNum'])) {
                return true;
            }

            Log::error('Ошибка отправки SMS через VernoInfo', [
                'phone' => $phone,
                'response' => $response->body(),
                'status_code' => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error('Исключение при отправке SMS через VernoInfo', ['phone' => $phone, 'error' => $e->getMessage()]);
        }

        return false;
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) == 11 && $phone[0] == '8') {
            $phone = '7'.substr($phone, 1);
        }
        if (strlen($phone) == 10) {
            $phone = '7'.$phone;
        }

        return '+'.$phone;
    }
}
