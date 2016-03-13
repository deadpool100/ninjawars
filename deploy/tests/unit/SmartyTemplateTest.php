<?php
class SmartyTemplateTest extends PHPUnit_Framework_TestCase {
    public function testSyntaxAll() {
        chdir("deploy");
        $engine = new NWTemplate();

        ob_start();
        $engine->compileAllTemplates(".tpl", true);
        $result = ob_get_contents();
        ob_end_clean();

        chdir("../");

        $failed_templates = [];
        preg_match_all('/^.*Syntax error.*$/im', $result, $failed_templates);

        $this->assertCount(0, $failed_templates[0], implode("\n", $failed_templates[0]));
    }
}
