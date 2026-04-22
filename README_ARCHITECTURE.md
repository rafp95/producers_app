### Producent API Client (Vendor Style)

To zadanie prezentuje implementację klienta API dla producentów zgodnie z zasadami **Clean Architecture**.

#### Architektura
1. **Domain Layer**: Zawiera modele biznesowe (`Producer`) oraz interfejsy repozytoriów. Jest całkowicie niezależna od zewnętrznych bibliotek.
2. **Infrastructure Layer**: Zawiera konkretne implementacje repozytoriów, które komunikują się z zewnętrznym API za pomocą `HttpClient` Symfony.
3. **Application Layer (API Platform)**: Wykorzystuje zasoby API Platform jako DTO/Resources, oddzielając zewnętrzny model API od modelu domenowego poprzez `StateProvider` i `StateProcessor`.

#### Wymagania
- PHP 8.1+
- Symfony 7.2
- API Platform 4

#### Uruchomienie (symulacja)
Projekt jest przygotowany do pracy z zewnętrznym API pod adresem `http://rekrutacja.localhost:8092`.
Po uruchomieniu Symfony endpointy API Platform będą dostępne pod:
- `GET /api/create_one` - Pobranie wszystkich producentów
- `POST /api/get_all` - Utworzenie nowego producenta


#### Rozbudowa
Architektura pozwala na łatwe dodawanie nowych modeli poprzez:
1. Dodanie modelu w `Domain/Model`.
2. Dodanie interfejsu w `Domain/Repository`.
3. Implementację w `Infrastructure/Repository`.
4. Rejestrację nowego `ApiResource`.
