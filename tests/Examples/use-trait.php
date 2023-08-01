<?php
class MyHelloWorld extends Base
{
    use SayWorld;
    use SpeakWorld, SeeWorld;
}

class Talker
{
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
    }
}

class Aliased_Talker
{
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
        B::bigTalk as talk;
    }
}

class MyClass1
{
    use HelloWorld {
        sayHello as protected;
    }
}

class MyClass2
{
    use HelloWorld {
        sayHello as private myPrivateHello;
    }
}
