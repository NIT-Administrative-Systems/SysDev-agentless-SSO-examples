# Agentless WebSSO Examples
This is a repository that contains example implementations of Northwestern's webSSO authentication. Some of the examples may also include Duo MFA as an additional step.

We are happy to accept pull requests with more examples!

## Setup
To authenticate a webSSO session, there is only one prerequisite: your app must be served from a `northwestern.edu` domain. The webSSO cookie is only accessible to `*.northwestern.edu` and `northwestern.edu` itself.

For Duo MFA, you need to contact the Identity Services team and request API keys (`IKEY`, `SKEY`, `AKEY`, and a `URL` for the callbacks).

## Beyond Authentication
WebSSO only gives you a netID. If you need directory information (e.g. name, email, staff/student/faculty status), you will need to use the [DirectorySearch service](https://apiserviceregistry.northwestern.edu). This beyond the scope of our humble demo repository.
