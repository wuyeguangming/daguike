/*
 * demo.cpp
 *
 *  Created on: 2014年7月22日
 *      Author: hadeswang
 */

#include <curl/curl.h>
#include <stdlib.h>
#include <stdio.h>
#include <sstream>
#include <iostream>

static size_t write_data(void *ptr, size_t sSize, size_t sNmemb, void *stream)
{
	std::string strBuf = std::string(static_cast<char *>(ptr), sSize * sNmemb);
	std::stringstream *ssResponse = static_cast<std::stringstream *>(stream);
	*ssResponse << strBuf;
	return sSize * sNmemb;
}

int main()
{
	CURL *pCurl;

	CURLcode eRetCode;

	eRetCode = curl_global_init(CURL_GLOBAL_SSL);
	if(CURLE_OK != eRetCode)
	{
		std::cout << "curl_global_init failed. RetCode:" << eRetCode << " ErrMsg:" << curl_easy_strerror(eRetCode) << std::endl;
	}

	pCurl = curl_easy_init();

	std::stringstream ssBody;

	curl_easy_setopt(pCurl, CURLOPT_TIMEOUT, 3);
	curl_easy_setopt(pCurl, CURLOPT_PROXY, "10.206.30.98");
	curl_easy_setopt(pCurl, CURLOPT_PROXYPORT, 8080);
	curl_easy_setopt(pCurl, CURLOPT_URL, "https://api.mch.weixin.qq.com/secapi/pay/refund");
	curl_easy_setopt(pCurl, CURLOPT_HEADER, true);
	curl_easy_setopt(pCurl, CURLOPT_POST, true);
	curl_easy_setopt(pCurl, CURLOPT_POSTFIELDS, "abc=dddd&kev=nnn");
	curl_easy_setopt(pCurl, CURLOPT_SSL_VERIFYHOST, 2);
	curl_easy_setopt(pCurl, CURLOPT_SSL_VERIFYPEER, true);
	//邮件中的文件 根CA证书
	curl_easy_setopt(pCurl, CURLOPT_CAINFO, "./rootca.pem");
	curl_easy_setopt(pCurl, CURLOPT_SSLCERTTYPE, "PEM");
	//邮件中的文件 客户证书（同样可以从apiclient_cert.p12文件导出，证书密码默认为商户号，如：10010000）
	//openssl pkcs12 -clcerts -nokeys -in apiclient_cert.p12 -out apiclient_cert.pem
	curl_easy_setopt(pCurl, CURLOPT_SSLCERT, "./apiclient_cert.pem");
	curl_easy_setopt(pCurl, CURLOPT_SSLKEYTYPE, "PEM");
	//邮件中的文件 密钥文件（公私钥，同样可以从apiclient_cert.p12文件导出，证书密码默认为商户号，如：10010000）
	//openssl pkcs12 -nocerts -in apiclient_cert.p12 -out apiclient_key.pem
	curl_easy_setopt(pCurl, CURLOPT_SSLKEY, "./apiclient_key.pem");

	curl_easy_setopt(pCurl, CURLOPT_WRITEFUNCTION, write_data);
	curl_easy_setopt(pCurl, CURLOPT_WRITEDATA, &ssBody);

	eRetCode = curl_easy_perform(pCurl);
	if(CURLE_OK != eRetCode)
	{
		std::cout << "curl_easy_perform failed. RetCode:" << eRetCode << " ErrMsg:" << curl_easy_strerror(eRetCode) << std::endl;
	}
	else
	{
		std::cout << "curl_easy_perform passed." << std::endl;
	}

	std::cout << "Content:" << ssBody.str() << std::endl;

	curl_easy_cleanup(pCurl);
	curl_global_cleanup();
}

