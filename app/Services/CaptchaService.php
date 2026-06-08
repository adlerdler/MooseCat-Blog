<?php

declare(strict_types=1);

namespace App\Services;

/**
 * 图片验证码服务（纯 GD 库，零外部依赖）
 *
 * 4 位字母数字验证码 + 干扰线 + 噪点，base64 输出
 *
 * 使用方式：
 *   $captcha = app(CaptchaService::class);
 *   $img     = $captcha->create();       // → data:image/png;base64,...
 *   $valid   = $captcha->check($input);  // → true/false（不区分大小写）
 */
class CaptchaService
{
    private const SESSION_KEY = 'captcha_code';
    private const ATTEMPTS_KEY = 'captcha_attempts';
    private const ATTEMPTS_WINDOW = 120; // 2 分钟窗口
    private const MAX_ATTEMPTS = 3; // 最多尝试 3 次

    private int $width  = 130;
    private int $height = 46;
    private int $fontSize = 20;
    private string $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * 生成验证码，返回 base64 图片字符串
     */
    public function create(): string
    {
        $code = '';
        $max  = strlen($this->charset) - 1;
        for ($i = 0; $i < 4; $i++) {
            $code .= $this->charset[random_int(0, $max)];
        }

        session()->put(self::SESSION_KEY, $code);

        return $this->render($code);
    }

    /**
     * 校验用户输入（不区分大小写，2 分钟内最多 3 次尝试）
     * 返回数组：['valid' => bool, 'remaining' => int]
     */
    public function check(string $input): array
    {
        $stored = session()->get(self::SESSION_KEY);
        if ($stored === null) {
            return ['valid' => false, 'remaining' => 0, 'message' => '验证码已失效，请刷新'];
        }

        // 检查尝试次数
        $attemptsData = session()->get(self::ATTEMPTS_KEY);
        $now = time();

        // 如果超过时间窗口，重置计数
        if ($attemptsData && ($now - $attemptsData['time']) > self::ATTEMPTS_WINDOW) {
            $attemptsData = null;
        }

        $attempts = $attemptsData ? $attemptsData['count'] : 0;

        if ($attempts >= self::MAX_ATTEMPTS) {
            session()->forget(self::SESSION_KEY);
            session()->forget(self::ATTEMPTS_KEY);
            return ['valid' => false, 'remaining' => 0, 'message' => '尝试次数已用完，请刷新验证码'];
        }

        $valid = strtoupper(trim($input)) === strtoupper($stored);

        if ($valid) {
            // 验证成功，删除验证码和尝试记录
            session()->forget(self::SESSION_KEY);
            session()->forget(self::ATTEMPTS_KEY);
            return ['valid' => true, 'remaining' => 0, 'message' => ''];
        }

        // 验证失败，增加尝试次数
        $newAttempts = $attempts + 1;
        session()->put(self::ATTEMPTS_KEY, [
            'count' => $newAttempts,
            'time' => $now,
        ]);

        $remaining = self::MAX_ATTEMPTS - $newAttempts;

        return [
            'valid' => false,
            'remaining' => $remaining,
            'message' => $remaining > 0 ? "验证码错误，还剩 {$remaining} 次尝试机会" : '尝试次数已用完，请刷新验证码',
        ];
    }

    /**
     * GD 渲染图片 → base64
     */
    private function render(string $code): string
    {
        $img = imagecreatetruecolor($this->width, $this->height);

        // 背景
        $bg = imagecolorallocate($img, 248, 248, 242);
        imagefill($img, 0, 0, $bg);

        // 边框
        $border = imagecolorallocate($img, 30, 30, 30);
        imagerectangle($img, 0, 0, $this->width - 1, $this->height - 1, $border);

        // 干扰线
        for ($i = 0; $i < 4; $i++) {
            $lc = imagecolorallocate($img, random_int(160, 210), random_int(160, 210), random_int(160, 210));
            imageline($img, random_int(5, 35), random_int(5, $this->height - 5), random_int($this->width - 35, $this->width - 5), random_int(5, $this->height - 5), $lc);
        }

        // 文字
        $font = $this->fontPath();
        $len  = strlen($code);
        $step = ($this->width - 20) / $len;

        for ($i = 0; $i < $len; $i++) {
            $tc = imagecolorallocate($img, random_int(0, 60), random_int(0, 60), random_int(0, 60));
            $x  = 10 + ($i * $step) + random_int(-3, 3);
            $y  = 32 + random_int(-5, 5);

            if ($font && function_exists('imagettftext')) {
                imagettftext($img, $this->fontSize, random_int(-15, 15), (int)$x, (int)$y, $tc, $font, $code[$i]);
            } else {
                imagestring($img, 5, (int)$x, (int)($y - 8), $code[$i], $tc);
            }
        }

        // 噪点
        for ($i = 0; $i < 60; $i++) {
            $dc = imagecolorallocate($img, random_int(130, 200), random_int(130, 200), random_int(130, 200));
            imagesetpixel($img, random_int(0, $this->width), random_int(0, $this->height), $dc);
        }

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($data);
    }

    /**
     * 查找可用的 TTF 字体路径
     */
    private function fontPath(): string
    {
        $candidates = [
            base_path('storage/fonts/NotoSansMono-Bold.ttf'),
            'C:\Windows\Fonts\arialbd.ttf',
            'C:\Windows\Fonts\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSansMono-Bold.ttf',
            '/usr/share/fonts/TTF/DejaVuSansMono-Bold.ttf',
            '/usr/share/fonts/dejavu/DejaVuSansMono-Bold.ttf',
        ];

        foreach ($candidates as $path) {
            try {
                if (@file_exists($path)) {
                    return $path;
                }
            } catch (\Throwable) {
                // open_basedir 限制，跳过
            }
        }

        return '';
    }
}
