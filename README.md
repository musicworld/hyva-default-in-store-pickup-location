
# Hyva Default In-Store Pickup Location

Dieses Modul fügt eine Standard-Abholstation in Magento 2 hinzu und ist für die Nutzung mit dem Hyvä-Theme optimiert.

## Installation

### Voraussetzungen

- Magento 2
- Hyvä Theme installiert und konfiguriert

### Schritte zur Installation

1. **Repository hinzufügen**

   Füge das Repository zur Composer-Konfiguration hinzu:
   ```bash
   composer config repositories.hyva-default-in-store-pickup-location git https://github.com/musicworld/hyva-default-in-store-pickup-location.git
   ```

2. **Paket installieren**

   Installiere das Modul via Composer:
   ```bash
   composer require musicworld/hyva-default-in-store-pickup-location:dev-main
   ```

3. **Modul aktivieren**

   Aktiviere das Modul und aktualisiere den Cache:
   ```bash
   php bin/magento module:enable Musicworld_HyvaDefaultInStorePickupLocation
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   ```
