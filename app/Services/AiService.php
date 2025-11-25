<?php

namespace App\Services;

class AiService {
    public function getCareerAdvice(string $pergunta, ?string $arqConteudo = null, ?string $mime = null): string {
        $idioma = $_SESSION['lang'] ?? 'pt-BR';
        $trad = require __DIR__ . '/../../config/lang.php';
        $t = $trad[$idioma] ?? $trad['pt-BR'];

        if (empty($pergunta)) return $t['ask_question'];
        
        $chave = $_ENV['GEMINI_API_KEY'] ?? null;
        if (!$chave) return $t['ai_not_configured'];
        
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent?key=' . $chave;
        $contexto = "Você é um mentor especializado em carreiras artísticas e musicais independentes, com profundo conhecimento em:\n\n" .
            "1. ESTRATÉGIAS DE CARREIRA: Planejamento de lançamentos, construção de marca pessoal, networking, gestão de tempo e produtividade artística.\n" .
            "2. MARKETING DIGITAL: Redes sociais (Instagram, TikTok, YouTube), algoritmos, engajamento orgânico, anúncios pagos, SEO para artistas.\n" .
            "3. MONETIZAÇÃO: Streaming (Spotify, Apple Music), shows ao vivo, merchandising, crowdfunding, licenciamento, direitos autorais, ECAD.\n" .
            "4. PRODUÇÃO MUSICAL: Home studio, mixagem básica, masterização, escolha de equipamentos, softwares (DAWs), colaborações.\n" .
            "5. ASPECTOS LEGAIS: Contratos, direitos autorais, direitos conexos, registro de obras, CNPJ MEI para artistas.\n" .
            "6. SAÚDE MENTAL: Gestão de ansiedade, síndrome do impostor, equilíbrio vida-arte, lidar com críticas e rejeição.\n" .
            "7. ANÁLISE DE DADOS: Interpretar métricas do Spotify for Artists, YouTube Analytics, Instagram Insights para tomar decisões estratégicas.\n\n" .
            "INSTRUÇÕES DE RESPOSTA:\n" .
            "- Seja empático, motivador e prático\n" .
            "- Forneça exemplos concretos e acionáveis\n" .
            "- Adapte a linguagem ao nível do artista (iniciante/intermediário/avançado)\n" .
            "- Se houver imagem anexada, analise elementos visuais (identidade visual, palco, equipamentos, arte de capa)\n" .
            "- Se houver áudio anexado, comente sobre produção, mixagem, arranjo, potencial comercial\n" .
            "- Cite ferramentas, plataformas e recursos específicos quando relevante\n" .
            "- Mantenha respostas entre 150-300 palavras\n" .
            "- Use português brasileiro natural, sem markdown\n\n" .
            "PERGUNTA DO ARTISTA: " . htmlspecialchars($pergunta, ENT_QUOTES, 'UTF-8');
        
        $partes = [['text' => $contexto]];
        $arqConteudo && $mime && $partes[] = ['inline_data' => ['mime_type' => $mime, 'data' => base64_encode($arqConteudo)]];
        
        $dados = [
            'contents' => [['parts' => $partes]],
            'generationConfig' => ['temperature' => 0.8, 'topK' => 40, 'topP' => 0.95, 'maxOutputTokens' => 1024, 'candidateCount' => 1],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $resp = curl_exec($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($codigo == 200 && $resp) {
            $resultado = json_decode($resp, true);
            $texto = $resultado['candidates'][0]['content']['parts'][0]['text'] ?? null;
            return $texto !== null ? nl2br(htmlspecialchars($texto, ENT_QUOTES, 'UTF-8')) : $t['error_processing'];
        }
        return ($codigo == 400 || $codigo == 403) ? $t['auth_error'] : $t['ai_unavailable'];
    }
}
