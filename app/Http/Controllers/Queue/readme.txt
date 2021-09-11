使用：

1.配置 /config/queue.php

2.更改 .env 文件配置 QUEUE_CONNECTION=database

开发过程：

1.迁移队列所需要的数据表
    生成迁移文件
        php artisan queue:table
    执行迁移
        1.迁移所有
            php artisan migrate
        2.指定迁移
            php artisan migrate --path=/database/migrations/2021_09_11_022815_create_failed_jobs_table.php
    创建任务类
        php artisan make:job SendEmail
2.编写任务类
    /app/Http/jobs/SendEmail.php
3.推送任务到队列
    /app/Http/Controllers/Queue/QueueController.php
4.运行队列监听器
    php artisan queue:listen
5.处理失败任务
    如果没有迁移 failed_table 表执行
        php artisan queue:failed-table
        执行迁移

* 其他常用命令
    1.查看表内错误记录
    php artisan queue:failed
    2.重新执行任务
    php artisan queue:retry 1    // 这里的 1 是失败记录表的ID
    php artisan queue:retry all  // 执行所有的失败记录
    3.删除失败任务
    php artisan queue:forget 1   // 这里的 1 是失败记录表的ID
    php artisan queue:flush      // 删除所有失败记录
