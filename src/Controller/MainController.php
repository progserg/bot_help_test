<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserMessage;
use App\Repository\UserMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/index/result')]
    public function result(UserMessageRepository $userMessageRepository): Response
    {
        $results = $userMessageRepository->getSomeResults();

        if (count($results) > 0) {
            
            $response = [
                '<table title="Таблица с результатами. Да, выглядит ужасно, но мы же тут про backend😶‍🌫️🙄😎">',
                '<thead>',
                '<th>id</th>',
                '<th>id пользователя</th>',
                '<th>сообщение</th>',
                '<th>сгенерировано(получено по API)</th>',
                '<th>обработано(отправлено пользователю)</th>',
                '</thead>',
                '<tbody>',
            ];
            $dateFormat = 'H:i:s d-m-Y';
            foreach ($results as $result) {
                $response[] = '<tr>';
                $response[] = "<td>{$result->getId()}</td>";
                $response[] = "<td>{$result->getUserId()}</td>";
                $response[] = "<td>{$result->getMessage()}</td>";
                $response[] = "<td>{$result->getGeneratedAt()?->format($dateFormat)}</td>";
                $response[] = "<td>{$result->getProcessedAt()?->format($dateFormat)}</td>";
                $response[] = '</tr>';
            }
            $response[] = '</tbody>';
            $response[] = '</table>';
        } else {
            $response[] = '<h1>Ничего нет</h1>';
        }
        return new Response(
            '<html>
                        <head>
                            <style>
                                table {
                                    border-radius: 3%;
                                }
                                table, th, td {
                                    border: 1px solid black;
                                    align-self: flex-start;
                                    padding: 10px;
                                }
                                th, td {
                                    border-radius: 7%;
                                }
                                td {
                                    text-align: center;
                                }
                            </style>
                        </head>
                        <body style="justify-content: center; display: flex;">' . implode('', $response) . '</body>
                        </html>'
        );
    }
}