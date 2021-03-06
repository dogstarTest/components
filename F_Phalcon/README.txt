自从在开源中国看到Phalcon这个开源框架，并看了其开发文档后，觉得其文档不仅仅是在讲他的框架，也不仅仅是在说明PHP这门语言，而是更大程度上在讲解如何设计一个框架、如何高效优雅地进行软件开发。

感兴趣的童鞋强烈推荐也阅读一下：上链接：http://docs.phalconphp.com/en/latest/index.html

但因为Phalcon框架是用C写的扩展，看不到PHP相关的源代码，但其开发思想很值得学习、参考、借鉴，所以就按照其思路实现了其中的部分核心类，在这里分享一下。目的：

1、通过Phalcon的伟大思想自主实现Phalcon部分类，加深对其的理解，以及自已的编码能力；
2、作为通用的核心类，实现后合适地应用于日后的项目；
3、规范自己的开发流程（测试+示例+生成发布代码）；

暂时实现的只有两个类，分类是DI（ Dependency Injection）类和Loader类（Universal Class Loader），后续有时间可能会再实现其他的。感兴趣的同学也可以试下~

附件列表：
1、全部文件压缩包（源代码、发布代码、示例、测试）
2、运行的效果截图
3、其中FDI的示例代码（为查看方便，也可以在下载代码中查看）

PS：小编用刚写的自动加载类FLoader，替换现有一项目的自动加载类，居然可以完美替换而无任何报错，小小开心中~~~~

#使用方法#

1、生成发布版本
./build_release.sh 

2、运行测试用例
phpunit test/test_FDI.php 
phpunit test/test_FLoader.php

3、运行示例
php demo_FLoader.php
php demo_FDI.php

