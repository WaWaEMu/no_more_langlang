export interface FormInter {
    title: string;
    city: string;
    town: string;
    is_stray: boolean | null;
    type: string;
    color: string;
    fur_type: string;
    name: string;
    gender: string;
    age: string;
    is_neuter: boolean | null;
    sendable_city: string[];
    adoption_description: string;
    health_description: string;
    adoption_condition: string;
    blobs: ['', '', ''];
}