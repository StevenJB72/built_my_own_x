# 🧱 Simple Blockchain in Go

This is a minimalist blockchain implementation written in Go — under 200 lines of code — based on a tutorial from [mycoralhealth.medium.com](https://mycoralhealth.medium.com/code-your-own-blockchain-in-less-than-200-lines-of-go-e296282bcffc). It demonstrates how blockchains work at a basic level, including:

- SHA-256 hashing
- Blockchain data structure
- Genesis block creation
- Chain validation
- REST API for interacting with the blockchain

---

## 🚀 Getting Started

### Prerequisites

- Go installed (version 1.16+ recommended)
- `curl` or Postman (for sending POST requests)

### Install Dependencies

```bash
go get github.com/gorilla/mux
go get github.com/davecgh/go-spew/spew
go get github.com/joho/godotenv