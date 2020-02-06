# Agentless WebSSO Examples for Legacy WebSSO
This was a repository that containeds example implementations of Northwestern's webSSO authentication. Some of the examples may have also included Duo MFA as an additional step.

**:warning: This repository is now deprecated. Please see [Agentless-WebSSO](https://github.com/NIT-Administrative-Systems/Agentless-WebSSO) for new examples.**

## Agentless?
We've relied on the webSSO agent to provide authentication in the past. This is a module that gets plugged into the web server (e.g. Apache) and acts as a request interceptor, only allowing an HTTP request to make it all the way to your application if a valid webSSO session is detected. It'll add the netID in as a header and your app can receive that trusted value.

This repo is an example of agentless setups -- webSSO without having to install that additional module. The upside to this approach is that developers have more control over their app's authentication process (e.g. you can have non-netID auth methods instead of the agent's blanket requirement). It also gives us the capability to do webSSO in non-traditional environments (e.g. AWS lambda) that don't have a supported web server.

There are a couple of downsides, too: Duo MFA must be implemented separately (it isn't too hard, but it's one more thing you have to do), and you lose the safety of having a battle-tested webSSO agent standing between your code and the savage hordes of the internet.

## Setup
To authenticate a webSSO session, there is only one prerequisite: your app must be served from a `northwestern.edu` domain. The webSSO cookie is only accessible to `*.northwestern.edu` and `northwestern.edu` itself.

For Duo MFA, you need to contact the Identity Services team and request API keys (`IKEY`, `SKEY`, `AKEY`, and a `URL` for the callbacks).

## Beyond Authentication
WebSSO only gives you a netID. If you need directory information (e.g. name, email, staff/student/faculty status), you will need to use the [DirectorySearch service](https://apiserviceregistry.northwestern.edu). This beyond the scope of our humble demo repository.

## Credits
Special thanks goes to the McCormick IT group, who wrote the first agentless implementation that I'd seen. All of this is just copping off their code ;)
