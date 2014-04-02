# Github Issues MediaWiki Extension - Private Repos

Adds a tag to render a list of Github issues from private repos inline, using [basic authentication](https://developer.github.com/v3/auth/#basic-authentication) (OAuth).

## Setup

Update issues.php with the relevant github username and [personal access token](https://github.com/blog/1509-personal-api-tokens).

## Usage

```html
<githubissues repo="repo-name"/>
```

You can add a query string with any of the filters on the [issues list API endpoint](http://developer.github.com/v3/issues/#list-issues-for-a-repository).

```html
<githubissues repo="repo-name" query="sort=updated&direction=desc"/>
```

When the page is rendered, the titles and description of all referenced issues will
be rendered in place of the tag. You can set the header level for the titles with
the `header` attribute (the default is h3). For example:

```html
<githubissues header="h2" repo="repo-name"/>
```

You can specify how long you would like to cache the list from Github by specifying the
number of hours in the `cache` attribute. The default is 2 hours.

```html
<githubissues cache="12" repo="repo-name"/>
```



# License

Copyright 2013 by Aaron Parecki

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.



