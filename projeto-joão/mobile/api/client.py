"""
api/client.py — Módulo de comunicação com a API REST do CheckInd.
Nunca acessa o banco diretamente. Toda comunicação é via HTTP.
"""

import requests
from typing import Optional


class ApiClient:
    """Cliente HTTP para a API REST do CheckInd."""

    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip("/")
        self.token: Optional[str] = None

    # ------------------------------------------------------------------
    # Helpers
    # ------------------------------------------------------------------

    def _headers(self) -> dict:
        """Retorna os cabeçalhos padrão, incluindo Authorization se disponível."""
        headers = {
            "Content-Type": "application/json",
            "Accept": "application/json",
        }
        if self.token:
            headers["Authorization"] = f"Bearer {self.token}"
        return headers

    def _get(self, endpoint: str) -> dict:
        """Realiza uma requisição GET e retorna o JSON."""
        try:
            url = f"{self.base_url}/{endpoint.lstrip('/')}"
            response = requests.get(url, headers=self._headers(), timeout=10)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.ConnectionError:
            return {"error": "Não foi possível conectar ao servidor. Verifique a rede."}
        except requests.exceptions.Timeout:
            return {"error": "A requisição excedeu o tempo limite. Tente novamente."}
        except requests.exceptions.HTTPError as e:
            try:
                return response.json()
            except Exception:
                return {"error": f"Erro HTTP {e.response.status_code}: {str(e)}"}
        except Exception as e:
            return {"error": str(e)}

    def _post(self, endpoint: str, data: dict) -> dict:
        """Realiza uma requisição POST e retorna o JSON."""
        try:
            url = f"{self.base_url}/{endpoint.lstrip('/')}"
            response = requests.post(url, json=data, headers=self._headers(), timeout=10)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.ConnectionError:
            return {"error": "Não foi possível conectar ao servidor. Verifique a rede."}
        except requests.exceptions.Timeout:
            return {"error": "A requisição excedeu o tempo limite. Tente novamente."}
        except requests.exceptions.HTTPError as e:
            try:
                return response.json()
            except Exception:
                return {"error": f"Erro HTTP {e.response.status_code}: {str(e)}"}
        except Exception as e:
            return {"error": str(e)}

    # ------------------------------------------------------------------
    # Endpoints
    # ------------------------------------------------------------------

    def login(self, email_or_matricula: str, password: str) -> dict:
        """
        Autentica o funcionário.
        POST /auth/login
        Armazena o token JWT em self.token em caso de sucesso.
        Retorna o dict completo da resposta (com 'employee' ou 'error').
        """
        payload = {
            "login": email_or_matricula.strip(),
            "password": password,
        }
        result = self._post("/auth/login", payload)
        if "token" in result:
            self.token = result["token"]
        return result

    def logout(self) -> dict:
        """
        Encerra a sessão do funcionário.
        POST /auth/logout
        Limpa o token local independente da resposta.
        """
        result = self._post("/auth/logout", {})
        self.token = None
        return result

    def get_machine(self, qr_token: str) -> dict:
        """
        Busca dados da máquina e checklist pelo token do QR Code.
        GET /machine/{token}
        Retorna dict com 'machine', 'checklist_type', 'checklist_items' ou 'error'.
        """
        return self._get(f"/machine/{qr_token}")

    def submit_log(self, machine_id: int, action_type: str, responses: dict) -> dict:
        """
        Envia as respostas do checklist e registra o log.
        POST /log
        - machine_id: ID da máquina
        - action_type: 'entry' ou 'exit'
        - responses: dict {item_id (str/int): bool}
        Retorna dict com 'status', 'message', 'log_id' ou 'error'.
        """
        # Converte para lista de dicts no formato esperado pelo backend
        formatted_responses = []
        for k, v in responses.items():
            formatted_responses.append({
                "item_id": int(k),
                "answer": bool(v)
            })

        payload = {
            "machine_id": machine_id,
            "action_type": action_type,
            "responses": formatted_responses,
        }
        return self._post("/log", payload)
