# 🚨 BookHive ERP — Server Troubleshooting Runbook

> **Live Server:** `http://129.212.231.122/`
> **Droplet:** DigitalOcean — `bookhive-erp` (Ubuntu 24.04)

---

## 👨‍🏫 For the Professor / Evaluator — If the Site is Down

> You do **not** need any server credentials or technical knowledge. Please follow these steps:

### Step 1 — Wait 60 Seconds and Refresh
The server is configured to **automatically self-heal**. If a service crashed, Docker will restart it within seconds on its own. Simply wait 1 minute and refresh the browser at `http://129.212.231.122/`.

### Step 2 — If Still Down, Contact the Development Team
Please reach out to any team member below and we will restore the server remotely within minutes:

| Name | Role | Contact |
| :--- | :--- | :--- |
| **Roma, Sean Justin** | Lead Developer & System Architect | seanromaa@gmail.com |
| **Bermejo, Kate Nicole** | Backend Developer | katenicolebermejo84@gmail.com |
| **Andura, Carla** | Database Administrator | *(contact via class group chat)* |

> The server runs on **DigitalOcean** in the cloud and does **not** require any local machine to be turned on. It runs 24/7 independently. Any downtime is temporary and can be resolved remotely by the team.

---

## 🛠️ For the Development Team — Technical Recovery Guide

This section is the **first thing to check** if the BookHive ERP site is down, unreachable, or throwing errors. Follow the steps in order.

---

## 🔴 Symptom 1: The Site Won't Load / Browser Shows "This site can't be reached"

**Cause:** The entire DigitalOcean Droplet may have rebooted, or the Docker containers are not running.

### Fix: SSH in and Restart the Stack

**Step 1 — Connect to the server via SSH:**
```bash
ssh root@129.212.231.122
```
> If you don't have the SSH key set up, use the **DigitalOcean Console** (browser-based terminal) from your Droplet's dashboard page as a fallback.

**Step 2 — Navigate to the project folder:**
```bash
cd ~/bookstore-erp
```

**Step 3 — Check which containers are running:**
```bash
docker ps
```
You should see **8 containers** listed with a status of **Up**. If any are missing or show `Exited`, proceed to Step 4.

**Step 4 — Restart all services:**
```bash
docker compose up -d
```

**Step 5 — Confirm all 8 are healthy:**
```bash
docker ps
```
Once all containers show **Up**, refresh the browser. ✅

---

## 🔴 Symptom 2: Site Loads but Login Shows "Error connecting to API Gateway"

**Cause:** The `database` container (MySQL) crashed due to low server memory.

### Fix: Check the Database and Restart

**Step 1 — SSH into the server:**
```bash
ssh root@129.212.231.122
```

**Step 2 — Check if the database container is down:**
```bash
docker ps -a
```
Look for `bookstore-erp-database-1`. If the STATUS column shows `Exited (137)`, it was killed by the OS for using too much RAM.

**Step 3 — Check if Swap memory is still active:**
```bash
free -h
```
The `Swap:` row should show `2.0Gi`. If it shows `0B`, the server was rebooted and swap was lost. Re-enable it:
```bash
sudo swapon /swapfile
```

**Step 4 — Restart the full stack:**
```bash
cd ~/bookstore-erp
docker compose up -d
```

**Step 5 — Verify the database is running:**
```bash
docker ps
```
`bookstore-erp-database-1` must show **Up**. The login page will work immediately. ✅

---

## 🔴 Symptom 3: A Specific Feature is Broken (e.g., Checkout, Reports, Inventory)

**Cause:** One of the individual microservice containers crashed or has an error.

### Fix: Inspect the Failing Microservice Logs

**Step 1 — SSH in and go to the project:**
```bash
ssh root@129.212.231.122
cd ~/bookstore-erp
```

**Step 2 — View logs for the specific service that is broken:**
```bash
# If login/register is broken:
docker compose logs auth-service

# If the book catalog or inventory is broken:
docker compose logs inventory-service

# If checkout or orders are broken:
docker compose logs order-service

# If dashboard reports/charts are broken:
docker compose logs reporting-service

# If ALL API routes are broken:
docker compose logs api-gateway
```

**Step 3 — Restart just the broken service:**
```bash
# Replace <service-name> with the name of the broken one
docker compose restart <service-name>

# Example:
docker compose restart auth-service
```

---

## 🔴 Symptom 4: Server Was Fully Rebooted (e.g., DigitalOcean maintenance)

**Cause:** After a full Droplet reboot, the Docker containers should auto-restart due to the `restart: unless-stopped` policy. However, the Swap memory file must be re-activated.

### Fix: Re-enable Swap and Verify Containers

```bash
ssh root@129.212.231.122

# 1. Re-enable the swap memory
sudo swapon /swapfile

# 2. Verify Swap is active (should show 2.0Gi)
free -h

# 3. Verify all containers restarted automatically
docker ps

# 4. If any containers are down, start them:
cd ~/bookstore-erp
docker compose up -d
```

> **💡 Tip:** To make swap permanent across reboots permanently, verify this line exists in `/etc/fstab`:
> ```
> /swapfile none swap sw 0 0
> ```
> Run `cat /etc/fstab` to confirm. If missing, add it with:
> ```bash
> echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
> ```

---

## 🔴 Symptom 5: Code Was Updated but Changes Are Not Showing on the Live Site

**Cause:** The running Docker containers are still using the old built image. You need to pull the latest code and rebuild.

### Fix: Pull Latest Code and Rebuild

```bash
ssh root@129.212.231.122
cd ~/bookstore-erp

# 1. Pull the latest code from GitHub
git pull origin main

# 2. Rebuild and restart all containers with the new code
docker compose up --build -d
```

---

## ✅ Quick Health Check Command (Copy-Paste This First)

When something seems wrong, run this single block to get a full picture of your server's health:

```bash
echo "=== SWAP MEMORY ===" && free -h && echo "" && echo "=== CONTAINER STATUS ===" && docker ps -a
```

This shows you both memory status and all container states in one shot.

---

## 📞 Team Contacts & Server Access

| Role | Name | Responsibility |
|:---|:---|:---|
| **Lead Developer / System Architect** | Roma, Sean Justin | Server access, Docker, GitHub |
| **Backend Developer** | Bermejo, Kate Nicole | Microservices, API issues |
| **Database Administrator** | Andura, Carla | Database errors, SQL issues |
| **Frontend Developer** | Garcia, Sophia Christi | UI/Frontend container issues |
| **QA & Documentation** | Labrador, Mariene | Testing and verifying fixes |

**Live Server IP:** `129.212.231.122`
**GitHub Repository:** `https://github.com/madebyseaan/ITSAR-ENDTERM`
