using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.IO;
using System.Net.Security;
using System.Security.Cryptography.X509Certificates;

namespace MCHDemo
{
    public partial class Form1 : Form
    {
        StringBuilder sb = new StringBuilder();
        
        public Form1()
        {
            InitializeComponent();
        }

        private void button1_Click(object sender, EventArgs e)
        {
            try
            {
                string url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
                string cert = @"R:\apiclient.p12";
                string password = "10010000";

                ServicePointManager.ServerCertificateValidationCallback = new RemoteCertificateValidationCallback(CheckValidationResult);
                X509Certificate cer = new X509Certificate(cert, password);
                HttpWebRequest webrequest = (HttpWebRequest)HttpWebRequest.Create(url);
                webrequest.ClientCertificates.Add(cer);
                webrequest.Method = "post";
                HttpWebResponse webreponse = (HttpWebResponse)webrequest.GetResponse();
                Stream stream = webreponse.GetResponseStream();
                string resp = string.Empty;
                using (StreamReader reader = new StreamReader(stream))
                {
                    resp = reader.ReadToEnd();
                }
                textBox1.Text = resp;
            }
            catch (Exception exp)
            {
                MessageBox.Show(exp.ToString());
            }
        }

        private static bool CheckValidationResult(object sender, X509Certificate certificate, X509Chain chain, SslPolicyErrors errors)
        {
            if (errors == SslPolicyErrors.None)
                return true;  
            return false; 
        }
    }
}
