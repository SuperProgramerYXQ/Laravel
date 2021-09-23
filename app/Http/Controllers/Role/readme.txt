中间件与权限管理的使用

1.新建表
    * 角色表
    CREATE TABLE `role` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `role_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '角色名称',
      `add_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    * 角色路由表
    CREATE TABLE `role_url` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `role_id` int(11) NOT NULL COMMENT '角色ID',
      `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '允许访问路由',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

    * 用户表
    CREATE TABLE `user` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
      `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
      `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为超管，1是  0否',
      `role_id` int(3) NOT NULL DEFAULT '0' COMMENT '角色ID',
      `add_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

2.编写伪登录逻辑（线上项目应为正常的登陆验证逻辑）
    App\Http\Controllers\Role\LoginController.php
    *****************************************************************************************
    * 前端获得登陆后返回的 token 自行保存,要求访问授权路由时将 token 放置于 header 进行访问 *
    *****************************************************************************************

3.创建中间件
    php artisan make:middleware CheckRole  // CheckRole 为中间件名称
    * 运行命令将会在 App\Http\Middleware 下生成一个 CheckRole.php 文件
    * 维持登陆中间件创建同理
    **************************************************************************
    * 登陆保持与权限验证应写于不同的中间件，这里为了方便演示写在同一个中间件 *
    **************************************************************************

4.注册中间件
    * 编辑 App\Http\Kernel.php
    * 在 $routeMiddleware 下增加中间件名称
    'CheckRole' => \App\Http\Middleware\CheckRole::class,

5.编辑中间件逻辑
    * App\Http\Middleware\CheckRole.php

6.路由写法
    写法解释参考 App\Http\Controllers\Role\LoginController.php 内 GetAllRoutes 方法解释

    // 单路由写法
    Route::get('RunEvent','Event\EventListenController@RunEvent')->name('路由名称')->middleware(CheckRole::class);

    // 路由分组写法
    Route::group(['middleware' => 'CheckRole'], function () {
        // 这里写需要验证权限的路由
        Route::get('RunEvent','Event\EventListenController@RunEvent')->name('路由名称'); // 带 name 属性为了标识是否需要验证权限及用于用户权限的添加
    });

用户增删改查逻辑自行处理，懒得写了，权限逻辑基本是这样，有什么更好的写法欢迎沟通
