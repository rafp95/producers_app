<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Producer;
use App\Domain\Repository\ProducerRepositoryInterface;
use DateTimeImmutable;

class InMemoryProducerRepository implements ProducerRepositoryInterface
{
    private string $filePath;

    public function __construct(string $projectDir)
    {
        $this->filePath = '/tmp/producers.json';
    }

    public function findAll(): array
    {
        return array_values($this->loadData());
    }

    public function save(Producer $producer): Producer
    {
        $producers = $this->loadData();
        
        $id = $producer->getId();
        if ($id === null) {
            $maxId = 0;
            foreach ($producers as $p) {
                if ($p->getId() > $maxId) {
                    $maxId = $p->getId();
                }
            }
            $id = $maxId + 1;
            // Tworzymy nową instancję z ID i timestampami
            $producer = new Producer($id, $producer->getName());
        }

        // Używamy metod z domeny do ustawienia timestampów
        $producer = $producer->withCreatedAt();
        
        $producers[$id] = $producer;
        $this->saveData($producers);

        return $producer;
    }

    /**
     * @return Producer[]
     */
    private function loadData(): array
    {
        if (!file_exists($this->filePath)) {
            // Próbujemy utworzyć plik, jeśli nie istnieje
            if (file_put_contents($this->filePath, json_encode([])) === false) {
                throw new \RuntimeException("Nie można utworzyć pliku: " . $this->filePath);
            }
            chmod($this->filePath, 0666);
            return [];
        }

        $content = file_get_contents($this->filePath);
        if ($content === false) {
            throw new \RuntimeException("Nie można odczytać pliku: " . $this->filePath);
        }
        
        $data = json_decode($content, true) ?: [];
        
        $producers = [];
        foreach ($data as $item) {
            if (isset($item['id'], $item['name'])) {
                $createdAt = isset($item['createdAt']) ? new DateTimeImmutable($item['createdAt']) : null;
                $updatedAt = isset($item['updatedAt']) ? new DateTimeImmutable($item['updatedAt']) : null;
                $producers[$item['id']] = new Producer(
                    (int)$item['id'], 
                    (string)$item['name'],
                    $createdAt,
                    $updatedAt
                );
            }
        }

        return $producers;
    }

    /**
     * @param Producer[] $producers
     */
    private function saveData(array $producers): void
    {
        $data = array_map(fn($p) => [
            'id' => $p->getId(),
            'name' => $p->getName(),
            'createdAt' => $p->getCreatedAt()?->format('Y-m-d\TH:i:s'),
            'updatedAt' => $p->getUpdatedAt()?->format('Y-m-d\TH:i:s')
        ], array_values($producers));

        if (file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
            throw new \RuntimeException("Nie można zapisać do pliku: " . $this->filePath);
        }
    }
}
