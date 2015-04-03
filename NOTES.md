NOTES
======

## PDB

an excerpt of a conversation on PDB (useful for debugging a python application):

[31/03/15 10:02:00] Davide Moro: metti una riga "import pdb; pdb.set_trace()"
[31/03/15 10:02:15] Davide Moro: riavvia
[31/03/15 10:02:22] Davide Moro: e poi devi usare i seguenti comandi:
[31/03/15 10:02:31] Davide Moro: l -> mostra dove ti trovi più o meno
[31/03/15 10:02:39] Davide Moro: l 112 -> ti mostra l'intorno della riga 112
[31/03/15 10:02:59] Davide Moro: c -> continua ed esci dal pdb, a meno che non l'abbia messo in un ciclo o becca un breakpoint
[31/03/15 10:03:18] Davide Moro: w -> mostra l'intero stack trace, in evidenza dove ti trovi
[31/03/15 10:03:28] Davide Moro: n -> next (non entra dentro le funzioni)
[31/03/15 10:03:37] Davide Moro: s -> step into (come next, ma entra nelle funzioni)
[31/03/15 10:04:05] Davide Moro: u -> sali nello stack per vedere sopra chi ha chiamato la tua func
[31/03/15 10:04:18] Davide Moro: d -> down, torni giù di una posizione nello stack
[31/03/15 10:04:33] Davide Moro: b 215 -> imposta un breakpoint alla linea 215


### Misc

```
//
// signed cookie:
// val < 40 - signature
// val > 40 - cookie val
//
// secret kotti values:
//  - prod: 3WxauDxXscfE,p2!
//  - dev: qwerty
//
//
// auth_tkt: cookie auth value
// beaker.session.id : cookie session
// 
// example auth_tkt value:
// "1ad057373be91d55b710382bc3122cf5106a9bf2ee6078f09f341d7d586e2ac08b859f5945a0784099ac8f5871c0637440fdc706079782d86531a9278fb9df8e55192212YWRtaW4=!userid_type:b64unicode"
//
```



