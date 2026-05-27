<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 测试登录 API - 成功登录
     */
    public function test_login_success()
    {
        $email = 'login-success@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ])
            ->assertJson([
                'token_type' => 'Bearer',
                'user' => [
                    'email' => $email,
                ],
            ]);

        $this->assertNotNull($response->json('access_token'));
    }

    /**
     * 测试登录 API - 无效凭据
     */
    public function test_login_with_invalid_credentials()
    {
        $email = 'invalid-creds@example.com';
        User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 测试登录 API - 缺少必填字段
     */
    public function test_login_with_missing_fields()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'missing-fields@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 测试登录 API - 无效邮箱格式
     */
    public function test_login_with_invalid_email_format()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 测试未认证访问受保护的 API - 应该返回401
     */
    public function test_unauthenticated_access_to_protected_api()
    {
        $response = $this->getJson('/api/v1/posts');
        $response->assertStatus(401);
    }

    /**
     * 测试使用token访问受保护的 API - 应该成功
     */
    public function test_authenticated_access_to_protected_api()
    {
        $email = 'auth-access@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/posts');

        $response->assertStatus(200);
    }

    /**
     * 测试使用无效token访问受保护的 API - 应该返回401
     */
    public function test_invalid_token_access_to_protected_api()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-12345',
        ])->getJson('/api/v1/posts');

        $response->assertStatus(401);
    }

    /**
     * 测试登出 API
     */
    public function test_logout_success()
    {
        $email = 'logout@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        // 通过登录 API 获取 token
        $loginResponse = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('access_token');

        // 验证登录后用户有一个 token
        $this->assertEquals(1, $user->tokens()->count());

        // 使用 token 登出
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);

        // 验证 token 已从数据库中删除
        $user->refresh();
        $this->assertEquals(0, $user->tokens()->count());
    }

    /**
     * 测试获取当前用户信息 API
     */
    public function test_get_current_user_info()
    {
        $email = 'current-user@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ])
            ->assertJson([
                'user' => [
                    'email' => $email,
                ],
            ]);
    }
}
