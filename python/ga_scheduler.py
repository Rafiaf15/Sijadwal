#!/usr/bin/env python3
import argparse
import json
import random
import time
from typing import Dict, List, Tuple


def evaluate_fitness(individual: List[Dict]) -> Tuple[float, int]:
    conflicts = 0
    teacher_slots = set()
    room_slots = set()

    for gene in individual:
        slot_key = f"{gene['hari']}|{gene['jam_mulai']}"
        teacher_key = f"{gene['dosen_id']}|{slot_key}"
        room_key = f"{gene['ruang_id']}|{slot_key}"

        if teacher_key in teacher_slots:
            conflicts += 1
        if room_key in room_slots:
            conflicts += 1

        teacher_slots.add(teacher_key)
        room_slots.add(room_key)

    fitness = max(0.0, 100.0 - (conflicts * 4.0))
    return fitness, conflicts


def create_individual(course_ids: List[int], dosen_ids: List[int], ruang_ids: List[int], slots: List[Dict]) -> List[Dict]:
    individual = []
    for course_id in course_ids:
        slot = random.choice(slots)
        individual.append(
            {
                "mata_kuliah_id": course_id,
                "dosen_id": random.choice(dosen_ids),
                "ruang_id": random.choice(ruang_ids),
                "hari": slot["hari"],
                "jam_mulai": slot["jam_mulai"],
                "jam_selesai": slot["jam_selesai"],
            }
        )
    return individual


def crossover(parent_a: List[Dict], parent_b: List[Dict]) -> List[Dict]:
    length = len(parent_a)
    if length <= 1:
        return parent_a[:]

    cut = random.randint(1, length - 1)
    return parent_a[:cut] + parent_b[cut:]


def mutate(individual: List[Dict], dosen_ids: List[int], ruang_ids: List[int], slots: List[Dict], mutation_rate: float) -> List[Dict]:
    child = []
    for gene in individual:
        new_gene = dict(gene)
        if random.random() <= mutation_rate:
            slot = random.choice(slots)
            new_gene["dosen_id"] = random.choice(dosen_ids)
            new_gene["ruang_id"] = random.choice(ruang_ids)
            new_gene["hari"] = slot["hari"]
            new_gene["jam_mulai"] = slot["jam_mulai"]
            new_gene["jam_selesai"] = slot["jam_selesai"]
        child.append(new_gene)
    return child


def solve(input_data: Dict) -> Dict:
    params = input_data.get("params", {})
    course_ids = input_data.get("course_ids", [])
    dosen_ids = input_data.get("dosen_ids", [])
    ruang_ids = input_data.get("ruang_ids", [])
    slots = input_data.get("slots", [])

    if not course_ids or not dosen_ids or not ruang_ids or not slots:
        return {
            "success": False,
            "message": "Input GA tidak lengkap. Pastikan course_ids, dosen_ids, ruang_ids, dan slots tersedia.",
        }

    population_size = max(10, min(100, int(params.get("population_size", 30))))
    max_generations = max(10, min(500, int(params.get("max_generations", 80))))
    mutation_rate = max(0.01, min(0.5, float(params.get("mutation_rate", 0.1))))

    start = time.time()

    population = [
        create_individual(course_ids, dosen_ids, ruang_ids, slots)
        for _ in range(population_size)
    ]

    best = None
    best_fitness = -1.0
    best_conflicts = 10**9

    for _ in range(max_generations):
        scored = []
        for individual in population:
            fitness, conflicts = evaluate_fitness(individual)
            scored.append((fitness, conflicts, individual))
            if fitness > best_fitness:
                best_fitness = fitness
                best_conflicts = conflicts
                best = individual

        scored.sort(key=lambda x: x[0], reverse=True)
        survivors = [item[2] for item in scored[: max(2, population_size // 2)]]

        next_population = survivors[:]
        while len(next_population) < population_size:
            parent_a = random.choice(survivors)
            parent_b = random.choice(survivors)
            child = crossover(parent_a, parent_b)
            child = mutate(child, dosen_ids, ruang_ids, slots, mutation_rate)
            next_population.append(child)

        population = next_population

    elapsed = time.time() - start
    schedule_count = len(best) if best else 0
    room_utilization = min(100.0, (schedule_count / max(1, len(ruang_ids) * len(slots))) * 100.0)
    teacher_preferences = max(50.0, 100.0 - (best_conflicts * 3.0))

    return {
        "success": True,
        "message": "Optimisasi selesai dari Python GA.",
        "final_fitness": round(best_fitness, 2),
        "conflicts": int(best_conflicts),
        "schedule_count": schedule_count,
        "execution_time_seconds": round(elapsed, 2),
        "generation_count": max_generations,
        "room_utilization": round(room_utilization, 2),
        "teacher_preferences": round(teacher_preferences, 2),
        "best_schedule": best or [],
    }


def main() -> None:
    parser = argparse.ArgumentParser(description="Genetic algorithm scheduler")
    parser.add_argument("--input", required=True, help="Input JSON path")
    parser.add_argument("--output", required=True, help="Output JSON path")
    args = parser.parse_args()

    random.seed()

    try:
        with open(args.input, "r", encoding="utf-8") as f:
            input_data = json.load(f)

        result = solve(input_data)
    except Exception as exc:
        result = {
            "success": False,
            "message": f"Python GA error: {str(exc)}",
        }

    with open(args.output, "w", encoding="utf-8") as f:
        json.dump(result, f, ensure_ascii=False)


if __name__ == "__main__":
    main()
