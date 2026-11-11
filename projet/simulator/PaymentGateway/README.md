# Payment gateway simulator

A simulated payment provider, for the workshop. It stands for a third-party library: its own words, its own formats, no network, no account.

Un prestataire de paiement simulé, pour l'atelier. Il tient le rôle d'une bibliothèque tierce : ses propres mots, ses propres formats, ni réseau, ni compte.

The Payment context talks to it only through `App\Payment\Infrastructure\GatewayDepositHolder` (ADR 0007).
