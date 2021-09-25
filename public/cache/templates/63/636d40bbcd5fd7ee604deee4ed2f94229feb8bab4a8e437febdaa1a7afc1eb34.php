<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* page.html.twig */
class __TwigTemplate_8bb90f593ebb8c78b63a56127bad9bea1d8ca176d0ec41ed8a150950fc6b7da7 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"";
        // line 2
        echo twig_escape_filter($this->env, ($context["langcode"] ?? null), "html", null, true);
        echo "\">
";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "head", [], "any", false, false, false, 3), "html", null, true);
        echo "
<body>
    <h1>Blog page</h1>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque officia enim et eius cumque nulla repellat voluptates. Laboriosam in rem numquam fuga, nam voluptate, facere labore aliquid dolorem vero deleniti deserunt necessitatibus, dolor minima maiores facilis delectus odio libero odit iure molestias pariatur magnam ratione. Suscipit iste ullam quos atque.</p>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"{{ langcode }}\">
{{ page.head }}
<body>
    <h1>Blog page</h1>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque officia enim et eius cumque nulla repellat voluptates. Laboriosam in rem numquam fuga, nam voluptate, facere labore aliquid dolorem vero deleniti deserunt necessitatibus, dolor minima maiores facilis delectus odio libero odit iure molestias pariatur magnam ratione. Suscipit iste ullam quos atque.</p>
</body>
</html>", "page.html.twig", "C:\\OpenServer\\domains\\blog\\templates\\page.html.twig");
    }
}
