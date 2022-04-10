CLI模式运行PHP的两种方式
* 第一种方法是使用 # php /path/to/yourFile.php 调用PHP CLI解释器，并给脚本传递参数。这种方法要指定php解释器的路径。
* 第二种方法是首先运行chmod +x <要运行的脚本文件名>（UNIX/Linux环境），将该PHP文件置为可执行权限，然后在CLI脚本头部第一行加入声明（类似于#! /usr/bin/php 或 PHP CLI 解释器位置），接着在命令行直接执行。

一、从命令行上读取参数
如果想从命令行获取参数，CLI可以从 $_SERVER['argv'] 和 $_SERVER['argc'] 取得参数的值和参数个数。
也可以直接使用$argv和$argc;

二、处理I/O通道
在PHP 5 CLI中，有一个文件流句柄，可以使用3个系统常量，分别为 STDIN、 STDOUT 和 STDERR。下面我们分别介绍。
(1) STDIN
STDIN 全称为 standard in 或 standard input 标准输入可以从终端取得任何数据。
(2) STDOUT
STDOUT 全称为 standard out 或 standard output 标准输出可以直接输出到屏幕(也可以输出到其他程序，使用STDIN取得)，如果在PHP CLI模式里使用print或echo语句，则这些数据将发送到STDOUT。
(3) STDERR
STDERR 全称为 standard error 在默认情况下会直接发送至用户终端，当使用STDIN文件句柄从其他应用程序没有读取到数据时会生成一个“stdin.stderr”。


三、用 nohup 后台运行CLI
如果正在运行一个进程，而且在退出账户时该进程还不会结束，即在系统后台或背景下运行，那么就可以使用nohup命令。该命令可以在退出账户之后继续运行相应的进程。
nohup在英文中就是不挂起的意思（no hang up）。该命令的一般形式为：

## nohup <脚本名.php> &

使用nohup命令提交作业，在默认情况下该作业的所有输出都被重定向到一个名为 nohup.out 的文件中，除非另外指定了输出文件：


 nohup /usr/local/php/bin/php ./scriptName.php 1>/tmp/log.txt &                将标准输出重定向到log.txt
 nohup /usr/local/php/bin/php ./scriptName.php 1>/tmp/log.txt 2>/tmp/err.txt & 将标准输出重定向到/tmp/log.txt 错误输出重定向到/tmp/err.txt
 nohup /usr/local/php/bin/php ./scriptName.php 1>/tmp/log.txt 2>&1 &           将标准输出重定向到/tmp/log.txt 然后再将标准错误输出重定向到标准输出，即：两者一起重定向到/tmp/log.txt
 nohup /usr/local/php/bin/php ./scriptName.php 1>/dev/null 2>&1 &              将标准输出和错误输出一起到重定向到/dev/null

这样，PHP CLI脚本执行后的结果将输出到log.txt中，我们可以使用tail命令动态查看内容：

## tail -f -n20 /tmp/log.txt

如何终止CLI程序的后台运行呢？
使用 # ps aux 可查看当前系统所有进程，使用grep过滤选项即可，如下：
## ps aux | grep php

https://zhuanlan.zhihu.com/p/367892971