# TODO
- Implement support of **$orderBy, $limit, $offset** in getList() in SqlGenericRepository and InMemoryGenericRepository
- Implement a merge strategy in SqlGenericRepository
- Create and throw extended exceptions in SqlBaseDriver (and SqlGenericRepository)
- Extend $criteria, add support of the following operators (and so other): < > != <= >= AND && OR ||
- Describe $criteria for Repository
- Add beginTransaction() and commitTransaction() to EntityManagerInterface  (and implement it, of course)
- Add expressions to orderBy() (e.g: ['1st raw SQL condition', '2nd raw SQL condition']) for SqlBaseDriver
