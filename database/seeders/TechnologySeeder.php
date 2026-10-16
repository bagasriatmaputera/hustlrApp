<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var array<string, list<string>> $data */
        $data = [
            'Frontend' => [
                'React.js',
                'Next.js',
                'Vue.js',
                'Livewire',
                'Angular',
                'Nuxt.js',
                'Svelte',
                'SvelteKit',
                'Astro',
                'Remix',
                'SolidJS',
                'Alpine.js',
                'HTMX',
                'Blade',
                'jQuery',
                'Gatsby',
            ],
            'Backend' => [
                'Laravel',
                'Golang',
                'Node.js',
                'Express.js',
                'NestJS',
                'Fastify',
                'Django',
                'FastAPI',
                'Flask',
                'Spring Boot',
                'ASP.NET Core',
                'Ruby on Rails',
                'Gin (Go)',
                'Fiber (Go)',
                'Actix Web (Rust)',
                'Axum (Rust)',
                'Phoenix (Elixir)',
            ],
            'Mobile' => [
                'Flutter',
                'React Native',
                'Swift (iOS)',
                'SwiftUI',
                'Kotlin (Android)',
                'Jetpack Compose',
                'Ionic',
                'Capacitor',
                'Expo',
            ],
            'Database' => [
                'MySQL',
                'PostgreSQL',
                'SQLite',
                'MongoDB',
                'Redis',
                'MariaDB',
                'Microsoft SQL Server',
                'Oracle Database',
                'Supabase',
                'Firebase Firestore',
                'DynamoDB',
                'Cassandra',
                'CouchDB',
                'Neo4j',
                'CockroachDB',
                'Meilisearch',
                'Elasticsearch',
            ],
            'DevOps & Cloud' => [
                'Docker',
                'Kubernetes',
                'AWS',
                'Google Cloud Platform (GCP)',
                'Microsoft Azure',
                'DigitalOcean',
                'Cloudflare',
                'Vercel',
                'Netlify',
                'Nginx',
                'Apache',
                'Terraform',
                'Ansible',
                'GitHub Actions',
                'GitLab CI',
                'Jenkins',
                'Linux',
                'Prometheus',
                'Grafana',
            ],
            'UI & Styling' => [
                'Tailwind CSS',
                'Bootstrap',
                'Sass / SCSS',
                'Material UI (MUI)',
                'Shadcn UI',
                'Ant Design',
                'Chakra UI',
                'Flux UI',
                'DaisyUI',
                'Radix UI',
                'Bulma',
            ],
            'Programming Language' => [
                'PHP',
                'Golang',
                'JavaScript',
                'TypeScript',
                'Python',
                'Rust',
                'Java',
                'Kotlin',
                'Swift',
                'C#',
                'C++',
                'Ruby',
                'Dart',
                'SQL',
                'Shell / Bash',
            ],
            'Testing & QA' => [
                'PHPUnit',
                'Pest',
                'Jest',
                'Vitest',
                'Cypress',
                'Playwright',
                'Selenium',
                'Postman',
                'Mockery',
            ],
            'AI & Data Science' => [
                'OpenAI API',
                'Anthropic Claude API',
                'LangChain',
                'Ollama',
                'PyTorch',
                'TensorFlow',
                'Hugging Face',
                'Pandas',
                'NumPy',
                'Scikit-learn',
            ],
            'Tools & Collaboration' => [
                'Git',
                'GitHub',
                'GitLab',
                'Jira',
                'Trello',
                'Linear',
                'Figma',
                'Postman',
                'Swagger / OpenAPI',
                'Vite',
                'Webpack',
            ],
        ];

        foreach ($data as $category => $techNames) {
            foreach ($techNames as $name) {
                Technology::firstOrCreate(
                    ['tech_name' => $name],
                    ['category' => $category]
                );
            }
        }
    }
}
