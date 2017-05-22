<?php
/**
 * User: ketu.lai <ketu.lai@gmail.com>
 * Date: 2017/5/22 10:27
 */

namespace Veda\Flash;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class AppValueResolver implements ArgumentValueResolverInterface
{

    //flash app
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return App::class === $argument->getType() || is_subclass_of($argument->getType(), App::class);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app;
    }
}