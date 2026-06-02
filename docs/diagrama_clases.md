# Diagrama de Clases (Representación en Texto)

+-----------------------------------------+
|                Database                 |
+-----------------------------------------+
| - host: string                          |
| - db_name: string                       |
| - username: string                      |
| - password: string                      |
| - conn: PDO                             |
+-----------------------------------------+
| + getConnection(): PDO                  |
+-----------------------------------------+
                    ^
                    | (usa)
                    |
+-----------------------------------------+
|                 Product                 |
+-----------------------------------------+
| - conn: PDO                             |
| - table_name: string                    |
| + product_id: int                       |
| + name: string                          |
| + sku: string                           |
| + price: float                          |
| + created_at: string                    |
+-----------------------------------------+
| + __construct(db: PDO)                  |
| + readAll(): array                      |
| + readById(id: int): array|false        |
| + create(): bool                        |
| + update(): bool                        |
| + delete(id: int): bool                 |
+-----------------------------------------+
                    ^
                    | (instancia y usa)
                    |
+-----------------------------------------+
|            ProductController            |
+-----------------------------------------+
| - productModel: Product                 |
+-----------------------------------------+
| + __construct()                         |
| + index(): array                        |
| + show(id: int): array|false            |
| + store(postData: array): void          |
| + update(postData: array): void         |
| + destroy(id: int): void                |
+-----------------------------------------+
                    | (usa)
                    v
+-----------------------------------------+
|                Response                 |
+-----------------------------------------+
| + redirect(url: string): void           |
+-----------------------------------------+
