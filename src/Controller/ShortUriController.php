<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Link;
use App\Shortener\Shortener;
use App\Shortener\ValidationException;
use Psr\Log\LoggerInterface;
use Sabre\Uri;
use Sabre\Uri\InvalidUriException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShortUriController extends AbstractController
{
    /**
     * @Route("/{path}")
     *
     * @param string          $path
     * @param Shortener       $shortener
     * @param LoggerInterface $logger
     *
     * @return Response
     */
    public function restore(string $path, Shortener $shortener, LoggerInterface $logger): Response
    {
        try {
            $link = $shortener->restoreLink($path);

            return new RedirectResponse($link->getUri());
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), [$e]);

            return JsonResponse::create([], 400);
        }
    }

    /**
     * @Route("/", methods={"GET"})
     *
     * @param Shortener       $shortener
     * @param Request         $request
     * @param LoggerInterface $logger
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function shortify(Shortener $shortener, Request $request, LoggerInterface $logger): Response
    {
        $link = (new Link())
            ->setUri($request->query->get('uri', ''))
            ->setExpireAt(new \DateTimeImmutable($request->query->get('expire_at', '+2 days')));

        try {
            /**
             * @var string
             * @var Link   $link
             */
            [$shortUri, $link] = $shortener->shortifyLink($link);

            return JsonResponse::create([
                'short_uri' => Uri\resolve($request->getSchemeAndHttpHost(), $shortUri),
                'origin_uri' => \urldecode($link->getUri()),
                'expired_at' => $link->getExpireAt(),
            ]);
        } catch (ValidationException | InvalidUriException $e) {
            $logger->error('Failed to validate Link', [$e]);
            $response = JsonResponse::create([], 400);
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), [$e]);
            $response = JsonResponse::create([], 500);
        }

        return $response;
    }
}
