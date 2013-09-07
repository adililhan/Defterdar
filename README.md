CHANGELOG.md=========

Nicely formatted changelog generator for your git repository.

### usage 

Defterdar works with __"git tags"__.

For example:

```
git clone https://github.com/USERNAME/YOUR_REPO.git

echo 'Test' > README.md

git commit -a -m "test commit"

git tag v1

git push origin v1

```

### action 

```bash
$ php defterdar.php
```
After that, look at CHANGELOG.md to see the changes.


### Are you a ROR(Ruby on Rails) Developer and looking for something more hipster?

No worries.

See __[Katip Project](https://github.com/kebab-project/katip/)__.
