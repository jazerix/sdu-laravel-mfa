<?php

namespace SDU\MFA;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class Authenticate
{
    private $ignore = ['sdu.mfa.callback', 'sdu.mfa.setup', 'sdu.mfa.forbidden'];

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->route()->getName(), $this->ignore))
            return $next($request);

        if ($request->user() == null)
        {
            return redirect($this->redirectUri($request->getUri()));
        }
        return $next($request);
    }

    private function redirectUri(string $intended)
    {
        /** @var Repository $config */
        $config      = app(Repository::class);
        $clientId    = $config->get('sdu-mfa.client_id');
        $tenantId    = $config->get('sdu-mfa.tenant_id');
        $redirectUri = route('sdu.mfa.callback');
        $state       = $this->state($intended);
        return "https://login.microsoftonline.com/$tenantId/oauth2/authorize?client_id=$clientId&response_type=code&redirect_uri=$redirectUri&response_mode=query&state=$state&resource=https://graph.microsoft.com";
    }

    private function state(string $intended)
    {
        $state = [
            'id'  => app(Session::class)->getId(),
            'uri' => $intended
        ];
        return base64_encode(json_encode($state));
    }
}