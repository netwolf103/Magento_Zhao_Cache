# Magento_Zhao_Cache
Magento加速扩展：主要用来加速Catalog Product、Catalog Category、CMS Page页面，支持Disk & Redis。使用这个模块后能加速这些页面相应速度至少50%以上。
<h4>如何安装？</h4>
下载 https://github.com/netwolf103/Magento_Zhao_Cache/archive/master.zip 到本地解压后直接覆盖到Magento根目录。
<h4>如何启用Cache？</h4>
登录Magento后台：Cache->Config system->Configuration->Zhao Cache，启用。
<h4>如何启用Redis？</h4>
编辑app/etc/local.xml，global 节点下增加：

```xml
<cache>
        <backend>Mage_Cache_Backend_Redis</backend>
        <backend_options>
                <server>127.0.0.1</server>
                <port>6379</port>
                <persistent></persistent>
                <database>0</database>
                <password></password>
                <force_standalone>0</force_standalone>
                <connect_retries>1</connect_retries>
                <read_timeout>10</read_timeout>
                <automatic_cleaning_factor>0</automatic_cleaning_factor>
                <compress_data>1</compress_data>
                <compress_tags>1</compress_tags>
                <compress_threshold>20480</compress_threshold>
                <compression_lib>gzip</compression_lib>
        </backend_options>
</cache>
```