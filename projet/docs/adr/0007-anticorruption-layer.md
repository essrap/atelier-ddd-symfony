# 7. An anticorruption layer between Payment and the payment provider

- Status: accepted
- Date: 2026-11-11

## Context

Before a rental, the renter leaves a deposit: the bank holds the amount until the equipment comes back. Holding money is the job of a payment provider. We cannot make the provider change its model (it serves thousands of clients), and its model does not suit ours: amounts as decimal strings (`'300.00'`), lowercase currencies (`'eur'`), refusals as string codes (`'insufficient_funds'`). Letting those words into `Payment` would spread the provider's choices through our domain, and each new version of its API would reach every place that reads them. The context map already says so since episode 3: anticorruption between the provider and `Payment`.

No real provider is used in this workshop: `simulator/PaymentGateway/` stands for one, outside `src/`, with its own namespace (`Simulator\PaymentGateway\`), so that it reads as a third-party library.

## Decision

- The domain states what it needs, in its own words: `DepositHolder` (`Payment/Domain`) holds and releases a deposit (`DepositId`, `Money`). A refusal is a domain exception, `DepositRefused`, with a reason of ours, `DepositRefusalReason` (`InsufficientFunds`, `CardExpired`, `Other`). It is not a `PaymentRuleBroken`: no rule of ours refuses, the bank does.
- The whole translation lives in one class of the infrastructure, `GatewayDepositHolder`, in both directions: our cents to their decimals and lowercase currency, their states and codes to our exception and reasons. Our `DepositId` is sent as the provider's reference, so no mapping table is kept.
- A refusal code we do not know becomes `Other`: the provider's strings never enter the domain. An unexpected state is a technical error (`\UnexpectedValueException`), not a domain one.
- Deptrac gets a `PaymentProvider` layer for the simulator: only `Payment` may use it. Inside `Payment`, nothing yet stops the domain from importing it: the rules per layer come in episode 17.
- The translation is tested against the simulator (`GatewayDepositHolderTest`). Mutation testing still covers the domain only (ADR 0003), not this infrastructure class.

## Alternatives

- Conformist: use the provider's model as it is. Fine when its model suits ours, as `Identity` does with Symfony Security; here it does not.
- Translating in each caller (an application service, a controller): the provider's words would leak in as many places as there are callers.
- Keeping the provider's codes as our reasons (`'insufficient_funds'` in the domain): a renamed code upstream would change our domain.
- A `retain()` method (keep the deposit after a dispute): `Dispute` does not exist yet.

## Consequences

- A change in the provider's API reaches one class and its test, not the domain: `exemples/ep10-casse/` renames a code upstream, and only `testInsufficientFundsBecomeOurReason` fails.
- Nothing calls `DepositHolder` outside its tests yet: holding the deposit when a booking is confirmed needs messages between contexts (CQRS module).
- A real provider would add HTTP, retries, webhooks and capture; none of them is in this workshop yet.
