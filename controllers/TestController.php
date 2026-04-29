<?php
class TestController {
    public function index() {
        echo "TestController index action works!";
    }
    public function hello($name = 'World') {
        echo "Hello, $name!";
    }
}