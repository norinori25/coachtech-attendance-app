<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    /**
     * メール認証誘導画面
     */
    public function notice()
    {
        $user = session('unauthenticated_user');
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        return view('auth.verify-email', compact('user'));
    }
    
    /**
     * メール認証実行（ログイン前）
     */
    public function verify(Request $request)
    {
        // セッションから未認証ユーザーを取得
        $user = session('unauthenticated_user');
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'セッションが切れました。再度ログインしてください。');
        }
        
        // URLのIDとユーザーIDが一致するか確認
        if (!hash_equals((string) $user->getKey(), (string) $request->route('id'))) {
            abort(403, '不正なリンクです。');
        }
        
        // ハッシュの検証
        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            abort(403, '不正なリンクです。');
        }
        
        // 署名の検証（有効期限チェック）
        if (!$request->hasValidSignature()) {
            abort(403, 'リンクの有効期限が切れています。');
        }
        
        // メール認証を完了
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }
        
        // セッションから未認証ユーザー情報を削除
        session()->forget('unauthenticated_user');
        
        // 自動ログイン
        auth()->login($user);
        
        return redirect('/attendance')
            ->with('verified', 'メール認証が完了しました！');
    }
    
    /**
     * 認証メール再送
     */
    public function resend(Request $request)
    {
        $user = session('unauthenticated_user');
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $user->sendEmailVerificationNotification();
        
        return back()->with('message', '認証メールを再送しました！');
    }
}