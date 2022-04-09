<?php


namespace app\util;


use PHPMailer\PHPMailer\PHPMailer;

class Mail
{


    protected static function getMailer(): PHPMailer
    {
        $config = ROOT_PATH . DIRECTORY_SEPARATOR . 'config' .DIRECTORY_SEPARATOR .'mail.php';
        $config = include $config;
        p($config);
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = $config['Host'];                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = $config['Username'];                // SMTP 用户名  即邮箱的用户名
        $mail->Password = $config['Password'];             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = $config['Port'];                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom($config['From'], $config['Mailer']);  //发件人
        $mail->addReplyTo($config['From'], $config['Mailer']); //回复的时候回复给哪个邮箱 建议和发件人一致

        return $mail;
    }

    public static function send($acceptor,$subject = '邮件标题',$contentBody = 'hi,this is an email',$altBody = '如果邮件客户端不支持HTML则显示此内容'){
        $mail = self::getMailer();

        if (is_array($acceptor) && isset($acceptor['address'])){
            $mail->addAddress($acceptor['address'],$acceptor['name']??$acceptor['address']);  // 收件人
        }else{
            $mail->addAddress($acceptor,$acceptor);  // 收件人
        }


        //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
        //$mail->addCC('cc@example.com');                    //抄送
        //$mail->addBCC('bcc@example.com');                    //密送

        //发送附件
        // $mail->addAttachment('../xy.zip');         // 添加附件
        // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = $subject;
        $mail->Body    = $contentBody;
        $mail->AltBody = $altBody;
        return $mail->send();
    }
}