<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) XiaoTeng <616896861@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers\Frontend;

use App\Models\Role;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\EmailSubscription;
use Illuminate\Support\Facades\Cache;

class IndexController extends FrontendController
{
    public function index()
    {
        $courses = Cache::remember('index_recent_course', 360, function () {
            return Course::published()->show()->orderByDesc('created_at')->limit(3)->get();
        });
        $roles = Cache::remember('index_roles', 360, function () {
            return Role::orderByDesc('weight')->limit(3)->get();
        });
        ['title' => $title, 'keywords' => $keywords, 'description' => $description] = config('meedu.seo.index');

        return view('frontend.index.index', compact('courses', 'roles', 'title', 'keywords', 'description'));
    }

    public function subscriptionHandler(Request $request)
    {
        $email = $request->input('email', '');
        if (! $email) {
            flash('请输入邮箱', 'warning');

            return back();
        }
        $exists = EmailSubscription::whereEmail($email)->exists();
        if (! $exists) {
            EmailSubscription::create(compact('email'));
        }
        flash('订阅成功', 'success');

        return back();
    }
}
