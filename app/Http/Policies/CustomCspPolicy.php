<?php

namespace App\Http\Policies;

use Spatie\Csp\Directive as CspDirective;
use Spatie\Csp\Policies\Policy;

class CustomCspPolicy extends Policy
{
    public function configure()
    {
        $this
            ->addDirective(CspDirective::BASE, 'self')
            ->addDirective(CspDirective::CONNECT, 'self')
            ->addDirective(CspDirective::DEFAULT, 'self')
            ->addDirective(CspDirective::FORM_ACTION, 'self')
            ->addDirective(CspDirective::IMG, ['self', 'data:'])
            ->addDirective(CspDirective::MEDIA, 'self')
            ->addDirective(CspDirective::OBJECT, 'none')
            ->addDirective(CspDirective::SCRIPT, ['self', 'unsafe-inline', 'unsafe-eval'])
            ->addDirective(CspDirective::STYLE, ['self', 'unsafe-inline'])
            ->addDirective(CspDirective::FONT, ['self', 'data:'])
            ->addNonceForDirective(CspDirective::SCRIPT)
            ->addNonceForDirective(CspDirective::STYLE)
            ->addDirective(CspDirective::FRAME_ANCESTORS, 'self');
    }
}
