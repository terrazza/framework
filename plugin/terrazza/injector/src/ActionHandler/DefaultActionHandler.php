<?php
namespace Terrazza\Injector\ActionHandler;

use Terrazza\Injector\ActionHandlerBuilderInterface;
use Terrazza\Injector\ActionHandlerInterface;
use Terrazza\Injector\ActionInterface;
use Terrazza\Injector\InjectorInterface;
use Terrazza\Injector\Exception\ActionHandlerException;

class DefaultActionHandler implements ActionHandlerInterface, ActionHandlerBuilderInterface {
    private array $actionMapper;
    private InjectorInterface $injector;
    public function __construct(InjectorInterface $injector, ?array $actionMapper) {
        $this->actionMapper                         = $actionMapper ?? [];
        $this->injector                             = $injector;
    }

    /**
     * @param ActionInterface $action
     * @return ActionHandlerInterface
     */
    private function getActionHandler(ActionInterface $action) : ActionHandlerInterface {
        $injectClass                                = get_class($action);
        if ($mappedClassName = $this->actionMapper[$injectClass]) {
            return $this->injector->get($mappedClassName);
        } else {
            throw new ActionHandlerException($injectClass." not found in actionMapper");
        }
    }

    /**
     * @template RR of mixed
     * @param ActionInterface<RR> $action
     * @return RR
     */
    public function execute(ActionInterface $action) {
        return $this->getActionHandler($action)->execute($action);
    }

    /**
     * @param array $actionMapper
     * @return ActionHandlerInterface
     */
    public function withMapper(array $actionMapper): ActionHandlerInterface {
        $handler                                    = clone $this;
        $handler->actionMapper                      = $actionMapper;
        return $handler;
    }

    /**
     * @return array
     */
    public function getActionMapper() : array {
        return $this->actionMapper;
    }
}