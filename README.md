# poweradmin-tools (archived)

This repository is archived and no longer maintained.

It held small PHP scripts (`fill_zones.php`, `fill_records.php`) that filled a
Poweradmin/PowerDNS database with random zones and records for testing. They
target the old single-database schema and do not work with current Poweradmin
releases, where the `zones` table lives in the separate Poweradmin database.

## Replacement

Use the perf data set in the main repository instead. It supports MySQL/MariaDB,
PostgreSQL and SQLite, with configurable sizes, users, groups and zone ownership:

```sh
# from a checkout of https://github.com/poweradmin/poweradmin
.devcontainer/scripts/import-test-data.sh --perf        # seed perf data
.devcontainer/scripts/import-test-data.sh --perf-clean  # remove it again
```

Sizes are set with environment variables such as `PERF_OWNED_ZONES`,
`PERF_RECORDS_PER_ZONE`, `PERF_BIG_ZONE_RECORDS`, `PERF_OTHER_ZONES`,
`PERF_USERS` and `PERF_GROUPS`. See `.devcontainer/sql/perf-data-*.sql`.
