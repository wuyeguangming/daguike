
weinre
npm -g install weinre
weinre  --boundHost 192.168.1.103 //ip替换为局域网能访问的ip
在需要调试的页面中加入（注意替换IP）
<script src="http://192.168.1.103:8080/target/target-script-min.js#anonymous"></script>


ngrok
客户端
点击运行run_me.bat即可，默认域名为wx.daguike.cn (配置项在ngrok.cfg中)
服务端：
登录：ssh -i aws.pem ubuntu@54.65.46.145
su
dkg880525
cd /root/ngrok
./run
aws.pem用于aws ssh

(goagent)
daguike-wx
idaguike@gmail.com
daguike880525

(以下为编译过程)
daguike.cn
cd /root/ngrok
openssl genrsa -out rootCA.key 2048
openssl req -x509 -new -nodes -key rootCA.key -subj "/CN=daguike.cn" -days 5000 -out rootCA.pem
openssl genrsa -out device.key 2048
openssl req -new -key device.key -subj "/CN=daguike.cn" -out device.csr
openssl x509 -req -in device.csr -CA rootCA.pem -CAkey rootCA.key -CAcreateserial -out device.crt -days 5000
cp rootCA.pem assets/client/tls/ngrokroot.crt
make clean
source /etc/profile
make release-server
GOOS=windows GOARCH=386 make release-client
cp bin/windows_386/ngrok.exe /var/www/html/
cp bin/ngrokd /var/www/html/
cp device.key /var/www/html/
cp device.crt /var/www/html/
/etc/init.d/apache2 start

daguike.com
wget http://daguike.cn/device.key
wget http://daguike.cn/device.crt
wget http://daguike.cn/ngrokd
vi run
nohup ./ngrokd -tlsKey=device.key -tlsCrt=device.crt -domain="daguike.com" &
or
nohup ./ngrokd -tlsKey=device.key -tlsCrt=device.crt -domain="daguike.com"  -httpAddr=":40001" -httpsAddr=":40002" &
chmod 777 ./*

windows
wget http://daguike.cn/ngrok.exe