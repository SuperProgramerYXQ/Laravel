# 线上模式应该关闭 DeBug 模式
.env 下 APP_DEBUG=true

# Http状态响应 abort(404) 直接向服务器抛出状态码，并展示 resources/views/errors/404.blade.php 内容

# 日志
# 配置文件位于 /config/logging.php 选择业务类型适用的模式，将其配置进 .env 文件中 LOG_CHANNEL=single
# 常用模式对应的应用场景
    single : 将日志记录追加进文件 /storage/logs/laravel.log 末尾位置
    daily : 当天的日志文件存于以当天日期为命名的日志文件中，例如：/storage/logs/laravel-2021-09-13.log（推荐这种模式，方便快速找到问题）
# 老版本配置文件位于 /config/app.php  对应的 .env 配置名为 APP_LOG=single
