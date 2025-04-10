#!/bin/bash
for file in $(grep -r "use App\\Models\\Admin\|use App\\Models\\Organisation" --include="*.php" app/Http/Controllers | cut -d: -f1 | sort | uniq); do echo "Fixing namespaces in $file"; sed -i "" "s/use App\\Models\\Admin\\/use App\\Models\\/g" "$file"; sed -i "" "s/use App\\Models\\Organisation\\/use App\\Models\\/g" "$file"; done
echo "Done fixing namespaces!"
